<?php namespace Jules\Ratings\Components;

use Cms\Classes\ComponentBase;
use Db;
use Input;
use Request;

class Vote extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Vote Component',
            'description' => 'Allows users to vote on items'
        ];
    }

    public function defineProperties()
    {
        return [
            'itemId' => [
                'title'       => 'Item ID',
                'description' => 'Unique identifier for the item being rated',
                'default'     => 'default',
                'type'        => 'string',
            ]
        ];
    }

    public function onRun()
    {
        $this->page['avgRating'] = $this->getAvgRating();
        $this->page['userRating'] = $this->getUserRating();
    }

    public function onRate()
    {
        $rating = Input::get('rating');
        $itemId = $this->property('itemId');
        $ip = Request::ip();

        if ($rating < 1 || $rating > 5) {
            throw new \Exception('Invalid rating');
        }

        // Simple check to prevent multiple votes from same IP for simplicity
        $existing = Db::table('jules_ratings_votes')
            ->where('item_id', $itemId)
            ->where('ip_address', $ip)
            ->first();

        if ($existing) {
            Db::table('jules_ratings_votes')
                ->where('id', $existing->id)
                ->update(['rating' => $rating, 'updated_at' => now()]);
        } else {
            Db::table('jules_ratings_votes')->insert([
                'item_id' => $itemId,
                'rating' => $rating,
                'ip_address' => $ip,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->page['avgRating'] = $this->getAvgRating();
        $this->page['userRating'] = $rating;

        return ['#rating-result' => $this->renderPartial('@result')];
    }

    protected function getAvgRating()
    {
        $itemId = $this->property('itemId');
        return Db::table('jules_ratings_votes')->where('item_id', $itemId)->avg('rating') ?: 0;
    }

    protected function getUserRating()
    {
        $itemId = $this->property('itemId');
        $ip = Request::ip();
        $vote = Db::table('jules_ratings_votes')
            ->where('item_id', $itemId)
            ->where('ip_address', $ip)
            ->first();
        return $vote ? $vote->rating : 0;
    }
}
