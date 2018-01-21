<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class FilesComments extends Migration
{
    public function up()  {
        $this->schema->create('files_comments', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_file')->unsigned();
            $table->integer('id_user')->unsigned();
            $table->text('text');
            $table->integer('time')->unsigned();
            $table->timestamps();

            $table->foreign('id_file')->references('id')->on('files_cache');
            $table->foreign('id_user')->references('id')->on('users');
        });
    }
    public function down()  {
        $this->schema->drop('files_comments');
    }
}
