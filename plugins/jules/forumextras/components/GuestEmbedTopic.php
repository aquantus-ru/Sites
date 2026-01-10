<?php namespace Jules\ForumExtras\Components;

use Winter\Forum\Components\EmbedTopic;
use Winter\Forum\Models\Topic as TopicModel;
use Winter\Forum\Models\Channel as ChannelModel;
use Exception;

class GuestEmbedTopic extends EmbedTopic
{
    public function componentDetails()
    {
        return [
            'name'        => 'Guest Embed Topic',
            'description' => 'Embeds a forum topic with guest posting support'
        ];
    }

    public function init()
    {
        $code = $this->property('embedCode');

        if (!$code) {
            throw new Exception('No code specified for the Forum Embed component');
        }

        $channel = ($channelSlug = $this->property('channelSlug'))
            ? ChannelModel::whereSlug($channelSlug)->first()
            : null;

        if (!$channel) {
            throw new Exception('No channel specified for Forum Embed component');
        }

        $properties = $this->getProperties();

        if ($topic = TopicModel::forEmbed($channel, $code)->first()) {
            $properties['slug'] = $topic->slug;
        }

        // Use GuestTopic
        $component = $this->addComponent('Jules\ForumExtras\Components\GuestTopic', $this->alias, $properties);

        if (!$topic) {
            $this->controller->bindEvent('page.end', function() use ($component, $channel, $code) {
                if ($component->embedMode !== false) {
                    $topic = TopicModel::createForEmbed($code, $channel, $this->page->title);
                    $component->setProperty('slug', $topic->slug);
                    $component->onRun();
                }
            });
        }

        $component->embedMode = 'single';
    }
}
