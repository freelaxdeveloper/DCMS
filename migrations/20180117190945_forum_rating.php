<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class ForumRating extends Migration
{
    public function up()  {
        $this->schema->create('forum_rating', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_message')->unsigned();
            $table->integer('id_user')->unsigned();
            $table->integer('time');
            $table->integer('rating');
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_message')->references('id')->on('forum_messages');

            $table->unique(['id_message', 'id_user']);
        });
    }
    public function down()  {
        $this->schema->drop('forum_rating');
    }
}
