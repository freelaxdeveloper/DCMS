<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class FilesCache extends Migration
{
    public function up()  {
        $this->schema->create('files_cache', function(Blueprint $table){
            $table->increments('id');
            $table->text('path_file_rel');
            $table->string('runame');
            $table->integer('time_add')->unsigned();
            $table->unsignedSmallInteger('group_show');
            $table->timestamps();

        });
    }
    public function down()  {
        $this->schema->drop('files_cache');
    }
}
