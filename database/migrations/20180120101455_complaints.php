<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class Complaints extends Migration
{
    public function up()  {
        $this->schema->create('complaints', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_user')->unsigned();
            $table->integer('id_ank')->unsigned()->comment('ID нарушителя');
            $table->integer('time')->unsigned();
            $table->text('comment')->comment('Комментарий к жалобе');
            $table->string('link')->comment('Ссылка на нарушение');
            $table->string('code')->comment('Нарушение');
            $table->enum('processed', ['0', '1'])->default(0)->comment('Значение об обработке');
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_ank')->references('id')->on('users');
        });
    }
    public function down()  {
        $this->schema->drop('complaints');
    }
}
