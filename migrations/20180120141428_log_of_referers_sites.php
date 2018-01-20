<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class LogOfReferersSites extends Migration
{
    public function up()  {
        $this->schema->create('log_of_referers_sites', function(Blueprint $table){
            $table->increments('id');
            $table->integer('time')->unsigned();
            $table->integer('count')->unsigned()->default(1)->comment('Количество переходов');
            $table->string('domain');
            $table->timestamps();
        });
    }
    public function down()  {
        $this->schema->drop('log_of_referers_sites');
    }
}
