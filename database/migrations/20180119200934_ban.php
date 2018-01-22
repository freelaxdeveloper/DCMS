<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class Ban extends Migration
{
    public function up()  {
        $this->schema->create('ban', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_user')->unsigned();
            $table->integer('id_adm')->unsigned();
            $table->string('link')->comment('ссылка на нарушение');
            $table->string('code')->comment('нарушение');
            $table->text('comment')->comment('Комментарий');
            $table->integer('time_start')->nullable()->comment('Начало действия бана');
            $table->integer('time_end')->nullable()->comment('Конец действия бана');
            $table->enum('access_view', ['0', '1'])->default(1)->comment('Гостевой доступ');
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_adm')->references('id')->on('users');
        });
    }
    public function down()  {
        $this->schema->drop('ban');
    }
}
