<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class GuestOnline extends Migration
{
    public function up()  {
        $this->schema->create('guest_online', function(Blueprint $table){
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->unsignedBigInteger('ip_long');
            $table->enum('is_robot', ['0', '1'])->default(0);
            $table->string('browser');
            $table->string('browser_ua')->comment('user_agent');
            $table->integer('time_start')->unsigned();
            $table->integer('time_last')->index();
            $table->string('domain')->comment('Домен');
            $table->string('request')->comment('Последняя страница');
            $table->integer('conversions')->unsigned()->default(1)->comment('Количество переходов	');
            $table->timestamps();
        });
    }
    public function down()  {
        $this->schema->drop('guest_online');
    }
}
