<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class ForumViews extends Migration
{
    public function up()  {
        $this->schema->create('forum_views', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_theme')->unsigned();
            $table->integer('id_user')->unsigned();
            $table->integer('views')->default(0);
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_theme')->references('id')->on('forum_themes');

            $table->unique(['id_theme', 'id_user']);
        });
    }
    public function down()  {
        $this->schema->drop('forum_views');
    }
}
