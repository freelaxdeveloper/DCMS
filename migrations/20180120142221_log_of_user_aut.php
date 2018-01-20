<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class LogOfUserAut extends Migration
{
    public function up()  {
        $this->schema->create('log_of_user_aut', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_user')->unsigned()->comment('ID юзера');
            $table->integer('user_id')->nullable()->unsigned()->comment('ID юзера ВК');
            $table->enum('method', ['cookie', 'post', 'get', 'vk'])->default('post')->comment('Method');
            $table->unsignedBigInteger('iplong')->comment('IP Адрес');
            #$table->ipAddress('iplong')->comment('IP Адрес');
            $table->integer('time')->unsigned()->comment('дата');
            $table->string('browser')->comment('Название браузера');
            $table->integer('id_browser')->unsigned()->comment('ID браузера');
            $table->string('browser_ua')->comment('user-agent');
            $table->string('domain')->comment('Домен');
            $table->enum('status', ['0', '1'])->default('0')->comment('Статус авторизации');
            $table->integer('count')->unsigned()->default(1)->comment('К-сть');
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('id_browser')->references('id')->on('browsers');
        });
    }
    public function down()  {
        $this->schema->drop('log_of_user_aut');
    }
}
