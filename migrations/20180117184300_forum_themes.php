<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class ForumThemes extends Migration
{
    public function up()  {
        $this->schema->create('forum_themes', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_category')->unsigned();
            $table->integer('id_topic')->unsigned();
            $table->integer('id_vote')->nullable();
            $table->string('name');
            $table->enum('top', ['0', '1'])->default(0)->comment('Вверху');
            $table->unsignedSmallInteger('group_show')->default(0)->comment('Права для просмотра раздела');
            $table->unsignedSmallInteger('group_write')->default(0)->comment('Права для создания тем в разделе	');
            $table->unsignedSmallInteger('group_edit')->default(0)->comment('Права для редактирования');
            $table->integer('id_autor')->unsigned()->comment('ID автора');
            $table->integer('time_create')->comment('Время создания');
            $table->integer('id_last')->unsigned()->comment('ID последнего написавшего');
            $table->integer('time_last')->comment('Время последнего сообщения');
            $table->integer('id_moderator')->nullable()->comment('ID модератора темы');
            $table->timestamps();

            $table->foreign('id_category')->references('id')->on('forum_categories');
            $table->foreign('id_topic')->references('id')->on('forum_topics');
            $table->foreign('id_autor')->references('id')->on('users');
            $table->foreign('id_last')->references('id')->on('users');
            //$table->foreign('id_vote')->references('id')->on('forum_vote');
            //$table->foreign('id_moderator')->references('id')->on('users');
        });
    }
    public function down()  {
        $this->schema->drop('forum_themes');
    }
}
