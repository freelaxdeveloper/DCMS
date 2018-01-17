<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class ForumVotes extends Migration
{
    public function up()  {
        $this->schema->create('forum_vote', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_autor')->unsigned();
            #$table->integer('id_theme')->unsigned();
            $table->unsignedSmallInteger('group_view')->default(0)->comment('Группа, которой доступен просмотр');
            $table->unsignedSmallInteger('group_vote')->default(1)->comment('Группа, которой доступно внесение голоса');
            $table->enum('active', ['0', '1'])->default(1)->comment('Признак активности');
            $table->string('name')->comment('Предмет голосования');
            $table->string('v1')->comment('Вариант ответа 1');
            $table->string('v2')->comment('Вариант ответа 2');
            $table->string('v3')->nullable()->comment('Вариант ответа 3');
            $table->string('v4')->nullable()->comment('Вариант ответа 4');
            $table->string('v5')->nullable()->comment('Вариант ответа 5');
            $table->string('v6')->nullable()->comment('Вариант ответа 6');
            $table->string('v7')->nullable()->comment('Вариант ответа 7');
            $table->string('v8')->nullable()->comment('Вариант ответа 8');
            $table->string('v9')->nullable()->comment('Вариант ответа 9');
            $table->string('v10')->nullable()->comment('Вариант ответа 10');
           
            $table->timestamps();

            $table->foreign('id_autor')->references('id')->on('users');
            #$table->foreign('id_theme')->references('id')->on('forum_themes');
        });
    }
    public function down()  {
        $this->schema->drop('forum_vote');
    }
}
