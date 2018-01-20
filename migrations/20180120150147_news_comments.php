<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class NewsComments extends Migration
{
    public function up()  {
        $this->schema->create('news_comments', function(Blueprint $table){
            $table->increments('id');
            $table->integer('time')->unsigned();
            $table->text('text');
            $table->integer('id_user')->unsigned();
            $table->integer('id_news')->unsigned();
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');            
            $table->foreign('id_news')->references('id')->on('news');            
        });
    }
    public function down()  {
        $this->schema->drop('news_comments');
    }
}
