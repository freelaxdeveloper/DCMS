<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class LogOfUserStatus extends Migration
{
    public function up()  {
        $this->schema->create('log_of_user_status', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_user')->unsigned();
            $table->integer('id_adm')->unsigned();
            $table->integer('time')->unsigned();
            $table->integer('type_last')->unsigned();
            $table->integer('type_now')->unsigned();
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_adm')->references('id')->on('users');
        });
    }
    public function down()  {
        $this->schema->drop('log_of_user_status');
    }
}
