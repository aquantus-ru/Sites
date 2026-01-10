<?php namespace Jules\ForumExtras;

use System\Classes\PluginBase;
use Event;
use Winter\Forum\Models\Member;

class Plugin extends PluginBase
{
    public $require = ['Winter.Forum', 'Winter.User'];

    public function boot()
    {
        Event::listen('winter.user.afterUpdate', function($user, $data) {
             if ($member = Member::getFromUser($user)) {
                 if (array_key_exists('signature', $data)) {
                     $member->signature = $data['signature'];
                     $member->save();
                 }
             }
        });
    }

    public function pluginDetails()
    {
        return [
            'name'        => 'Forum Extras',
            'description' => 'Guest posting and stats for Winter Forum',
            'author'      => 'Jules',
            'icon'        => 'icon-comments',
        ];
    }

    public function registerComponents()
    {
        return [
            'Jules\ForumExtras\Components\GuestEmbedTopic' => 'guestEmbedTopic',
            'Jules\ForumExtras\Components\GuestTopic'      => 'guestTopic',
            'Jules\ForumExtras\Components\ForumStats'      => 'forumStats',
        ];
    }
}
