<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class LogOfVisitsForDays extends Migration
{
    public function up()  {
        $this->schema->create('log_of_visits_for_days', function(Blueprint $table){
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->integer('time_day')->unsigned()->comment('Время на начало суток');
            $table->integer('hits_full')->unsigned()->default('0');
            $table->integer('hosts_full')->unsigned()->default('0');
            $table->integer('hits_light')->unsigned()->default('0');
            $table->integer('hosts_light')->unsigned()->default('0');
            $table->integer('hits_mobile')->unsigned()->default('0');
            $table->integer('hosts_mobile')->unsigned()->default('0');
            $table->integer('hits_robot')->unsigned()->default('0');
            $table->integer('hosts_robot')->unsigned()->default('0');
            #$table->timestamps();
        });
    }
    public function down()  {
        $this->schema->drop('log_of_visits_for_days');
    }
}
