<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class Friends extends Migration
{
    public function up()  {
        $this->schema->create('friends', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_friend')->unsigned()->comment('Друг');
            $table->integer('id_user')->unsigned()->comment('Пользователь');
            $table->integer('time')->unsigned()->nullable();
            $table->string('name')->nullable()->comment('Переопределение ника');
            $table->enum('confirm', ['0', '1'])->default(0);
            $table->timestamps();

            $table->foreign('id_friend')->references('id')->on('users');
            $table->foreign('id_user')->references('id')->on('users');
            $table->unique(['id_friend', 'id_user']);
        });
    }
    public function down()  {
        $this->schema->drop('friends');
    }
}
