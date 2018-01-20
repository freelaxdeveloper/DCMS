<?php
/**
 * Очередь отправляемых писем
 */
use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class MailQueue extends Migration
{
    public function up()  {
        $this->schema->create('mail_queue', function(Blueprint $table){
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->string('to');
            $table->string('title');
            $table->text('content');
        });
    }
    public function down()  {
        $this->schema->drop('mail_queue');
    }
}
