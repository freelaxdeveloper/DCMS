<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class Browsers extends Migration
{
    public function up()  {
        $this->schema->create('browsers', function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->enum('type', ['light', 'mobile', 'full']);
        });
    }
    public function down()  {
        $this->schema->drop('browsers');
    }
}
