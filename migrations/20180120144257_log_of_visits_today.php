<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class LogOfVisitsToday extends Migration
{
    public function up()  {
        $this->schema->create('log_of_visits_today', function(Blueprint $table){
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->integer('time')->unsigned();
            $table->integer('iplong')->unsigned();
             #$table->ipAddress('iplong');
            $table->string('browser')->comment('Название браузера');
            $table->integer('id_browser')->unsigned()->comment('ID браузера');
            $table->enum('browser_type', ['light', 'mobile', 'full', 'robot'])->comment('Тип браузера');
            #$table->timestamps();

            #$table->foreign('id_browser')->references('id')->on('browsers');
        });
    }
    public function down()  {
        $this->schema->drop('log_of_visits_today');
    }
}
