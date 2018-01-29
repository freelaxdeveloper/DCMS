<?php
/**
 * Пользователи online
 */
use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class UsersOnline extends Migration
{
    public function up()  {
        $this->schema->create('users_online', function(Blueprint $table){
            $table->increments('id');
            $table->integer('time_login')->unsigned()->nullable();
            $table->integer('time_last')->unsigned();
            $table->string('request')->nullable()->comment('Последняя страница');
            $table->integer('id_browser')->unsigned();
            $table->integer('id_user')->unsigned();
            $table->integer('conversions')->default(1);
            $table->unsignedBigInteger('ip_long');
            #$table->ipAddress('ip_long');

            $table->foreign('id_browser')->references('id')->on('browsers');            
            $table->foreign('id_user')->references('id')->on('users');            
        });
    }
    public function down()  {
        $this->schema->drop('users_online');
    }
}
