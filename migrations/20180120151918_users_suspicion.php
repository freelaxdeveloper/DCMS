<?php
/**
 * Подозрительные регистраци
 */
use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class UsersSuspicion extends Migration
{
    public function up()  {
        $this->schema->create('users_suspicion', function(Blueprint $table){
            $table->increments('id');
            $table->integer('id_user')->unsigned();
            $table->string('text');

            $table->foreign('id_user')->references('id')->on('users');            
        });
    }
    public function down()  {
        $this->schema->drop('users_suspicion');
    }
}
