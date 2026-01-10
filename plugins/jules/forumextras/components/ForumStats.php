<?php namespace Jules\ForumExtras\Components;

use Cms\Classes\ComponentBase;
use Winter\Forum\Models\Topic;
use Winter\User\Models\User;

class ForumStats extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Forum Stats',
            'description' => 'Displays forum statistics'
        ];
    }

    public function onRun()
    {
        $this->page['topTopics'] = Topic::orderBy('count_views', 'desc')->take(5)->get();
        $this->page['newUsers'] = User::orderBy('created_at', 'desc')->take(5)->get();
        $this->page['totalTopics'] = Topic::count();
        $this->page['totalUsers'] = User::count();
    }
}
