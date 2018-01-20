<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class FilesRatings extends Migration
{
    public function up()  {
        $this->schema->create('files_rating', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_file')->unsigned();
            $table->integer('id_user')->unsigned();
            $table->tinyInteger('rating')->unsigned();
            $table->timestamps();

            $table->foreign('id_file')->references('id')->on('files_cache');
            $table->foreign('id_user')->references('id')->on('users');
            $table->unique(['id_file', 'id_user']);
        });
    }
    public function down()  {
        $this->schema->drop('files_rating');
    }
}
