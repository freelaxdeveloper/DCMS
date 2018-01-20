<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class LoginHistory extends Migration
{
    public function up()  {
        $this->schema->create('login_history', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_user')->unsigned()->comment('ID пользователя');
            $table->integer('time')->unsigned()->comment('Время изменения');
            $table->string('login')->comment('Старый логин');
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');
        });
    }
    public function down()  {
        $this->schema->drop('login_history');
    }
}
