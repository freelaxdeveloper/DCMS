<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class ActionListAdministrators extends Migration
{
    public function up()  {
        $this->schema->create('action_list_administrators', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_user')->unsigned();
            $table->integer('time')->unsigned();
            $table->text('description');
            $table->string('module')->index();
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');
        });
    }
    public function down()  {
        $this->schema->drop('action_list_administrators');
    }
}
