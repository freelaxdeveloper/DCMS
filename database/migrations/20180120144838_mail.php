<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class Mail extends Migration
{
    public function up()  {
        $this->schema->create('mail', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_user')->unsigned()->comment('Пользователь (получатель)');
            $table->integer('id_sender')->unsigned()->comment('Отправитель');
            $table->integer('time')->comment('Время отправки');
            $table->enum('is_read', ['0', '1'])->default('0')->comment('Метка о прочтении');
            $table->text('mess')->comment('Сообщение');
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_sender')->references('id')->on('users');
        });
    }
    public function down()  {
        $this->schema->drop('mail');
    }
}
