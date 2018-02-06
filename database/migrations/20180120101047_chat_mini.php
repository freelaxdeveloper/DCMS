<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChatMini extends Migration
{
    public function up()  {
        $this->schema->create('chat_mini', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_user')->unsigned();
            $table->text('message');
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');
        });
    }
    public function down()  {
        $this->schema->drop('chat_mini');
    }
}
