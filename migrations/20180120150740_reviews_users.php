<?php
/**
 * Отзывы пользователей
 */
use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class ReviewsUsers extends Migration
{
    public function up()  {
        $this->schema->create('reviews_users', function(Blueprint $table){
            $table->increments('id');
            $table->integer('time')->unsigned();
            $table->integer('id_user')->unsigned()->comment('Оставивший отзыв, пользователь');
            $table->integer('id_ank')->unsigned();
            $table->integer('forum_message_id')->unsigned()->nullable();
            $table->text('text');
            $table->float('rating')->default(0);
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');            
            $table->foreign('id_ank')->references('id')->on('users');            
            $table->foreign('forum_message_id')->references('id')->on('forum_messages');            
        });
    }
    public function down()  {
        $this->schema->drop('reviews_users');
    }
}
