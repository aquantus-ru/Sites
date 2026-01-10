<?php namespace Jules\ForumExtras\Components;

use Winter\Forum\Components\Topic;
use Auth;
use Flash;
use Redirect;
use Exception;
use Winter\Forum\Models\Post as PostModel;
use Winter\Forum\Models\TopicFollow;
use Winter\Forum\Models\Member as MemberModel;
use Winter\User\Models\User as UserModel;

class GuestTopic extends Topic
{
    public function componentDetails()
    {
        return [
            'name'        => 'Guest Topic',
            'description' => 'Topic component with Guest support'
        ];
    }

    public function onPost()
    {
        try {
            $user = Auth::getUser();
            $member = null;

            if ($user) {
                $member = $this->getMember();
            } else {
                // Guest Logic
                $guestName = post('guest_name');
                if (empty($guestName)) {
                    throw new Exception('Please enter your name.');
                }

                // Find or Create Guest User
                $guestEmail = 'guest@coherent.sbs';
                $guestUser = UserModel::where('email', $guestEmail)->first();
                if (!$guestUser) {
                    $guestUser = new UserModel;
                    $guestUser->name = 'Guest';
                    $guestUser->email = $guestEmail;
                    $guestUser->password = 'guest123';
                    $guestUser->password_confirmation = 'guest123';
                    $guestUser->is_activated = true;
                    $guestUser->save();
                }

                $member = MemberModel::getFromUser($guestUser);
            }

            $topic = $this->getTopic();

            if (!$topic) {
                throw new Exception('Topic not found');
            }

            // Check permissions using the determined member
            if (!$topic->canPost($member)) {
                throw new Exception('You cannot edit posts or make replies.');
            }

            $data = post();
            if (!$user) {
                $guestName = post('guest_name');
                $content = post('content');
                $data['content'] = "**Guest ($guestName):**\n" . $content;
            }

            $post = PostModel::createInTopic($topic, $member, $data);
            $postUrl = $this->currentPageUrl([$this->paramName('slug') => $topic->slug]);

            try {
                TopicFollow::sendNotifications($topic, $post, $postUrl);
            } catch (Exception $e) {
                // Ignore mail errors
            }

            Flash::success(post('flash', 'Response added successfully!'));

            $redirectUrl = post('redirect', $postUrl);

            return Redirect::to($redirectUrl.'?page=last#post-'.$post->id);
        }
        catch (Exception $ex) {
            Flash::error($ex->getMessage());
        }
    }
}
