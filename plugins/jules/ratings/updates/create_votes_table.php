<?php namespace Jules\Ratings\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class CreateVotesTable extends Migration
{
    public function up()
    {
        Schema::create('jules_ratings_votes', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('item_id')->index(); // ID of the item being rated
            $table->integer('rating'); // 1-5
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jules_ratings_votes');
    }
}
