<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class MailUnsubscribe extends Migration
{
    public function up()  {
        $this->schema->create('mail_unsubscribe', function(Blueprint $table){
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('code')->index();
        });
    }
    public function down()  {
        $this->schema->drop('mail_unsubscribe');
    }
}
