<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class News extends Migration
{
    public function up()  {
        $this->schema->create('news', function(Blueprint $table){
            $table->increments('id');
            $table->integer('time')->unsigned();
            $table->string('title');
            $table->text('text');
            $table->integer('id_user')->unsigned();
            $table->enum('sended', ['0', '1'])->default(0)->comment('Пометка о рассылке');
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');            
        });
    }
    public function down()  {
        $this->schema->drop('news');
    }
}
