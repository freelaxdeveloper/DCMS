<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class Invations extends Migration
{
    public function up()  {
        $this->schema->create('invations', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_invite')->nullable()->unsigned()->comment('Приглашенный');
            $table->integer('id_user')->unsigned()->comment('Хозяин пригласительного');
            $table->integer('time_reg')->unsigned()->nullable()->comment('Время регистрации по пригласительному');
            $table->string('code')->nullable()->comment('код пригласительного');
            $table->string('email')->nullable()->comment('Email, на который отправлен пригласительный');
            $table->timestamps();

            $table->foreign('id_invite')->references('id')->on('users');
            $table->foreign('id_user')->references('id')->on('users');
            $table->unique(['id_invite', 'id_user']);
        });
    }
    public function down()  {
        $this->schema->drop('invations');
    }
}
