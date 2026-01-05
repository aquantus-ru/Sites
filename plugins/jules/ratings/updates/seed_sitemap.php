<?php namespace Jules\Ratings\Updates;

use Schema;
use Winter\Sitemap\Models\Definition;
use Winter\Storm\Database\Updates\Migration;

class SeedSitemapDefinition extends Migration
{
    public function up()
    {
        $definition = new Definition;
        $definition->theme = 'msn2007';
        $definition->data = [
            [
                'type' => 'url',
                'url' => '/',
                'changefreq' => 'always',
                'priority' => '1.0',
            ],
            [
                'type' => 'url',
                'url' => '/style-test',
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'type' => 'url',
                'url' => '/forum',
                'changefreq' => 'daily',
                'priority' => '0.9',
            ]
        ];
        $definition->save();
    }

    public function down()
    {
        //
    }
}
