<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class LogOfReferers extends Migration
{
    public function up()  {
        $this->schema->create('log_of_referers', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_site')->unsigned()->comment('Идентификатор сайта');
            $table->integer('time')->unsigned();
            $table->text('full_url')->comment('Полный URL');
            $table->timestamps();

            $table->foreign('id_site')->references('id')->on('log_of_referers_sites');
        });
    }
    public function down()  {
        $this->schema->drop('log_of_referers');
    }
}
