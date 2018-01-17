<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class ForumVoteVotes extends Migration
{
    public function up()  {
        $this->schema->create('forum_vote_votes', function(Blueprint $table){
            $table->increments('id');
            //$table->integer('id_theme')->unsigned();
            $table->integer('id_user')->unsigned();
            $table->integer('id_vote')->unsigned();
            $table->enum('vote', ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10']);
            $table->timestamps();

            $table->foreign('id_vote')->references('id')->on('forum_vote');
            $table->foreign('id_user')->references('id')->on('users');
            //$table->foreign('id_theme')->references('id')->on('forum_themes');
        });
    }
    public function down()  {
        $this->schema->drop('forum_vote_votes');
    }
}
