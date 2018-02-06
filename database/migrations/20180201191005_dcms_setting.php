<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class DcmsSetting extends Migration
{
    public function up()  {
        $this->schema->create('dcms_settings', function(Blueprint $table){
            $table->increments('id');
            $table->string('key');
            $table->json('options')->nullable();
            $table->timestamps();
        });
    }
    public function down()  {
        $this->schema->drop('dcms_settings');
    }
}
