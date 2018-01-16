<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class ForumCategories extends Migration
{
    public function up()  {
        $this->schema->create('forum_categories', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedSmallInteger('position')->comment('для сортировки');
            $table->string('name');
            $table->unsignedSmallInteger('group_show')->default(0)->comment('Права для просмотра раздела');
            $table->unsignedSmallInteger('group_write')->default(0)->comment('Права для создания тем в разделе	');
            $table->unsignedSmallInteger('group_edit')->default(0)->comment('Права для редактирования');
            $table->mediumText('description');

            //$table->softDeletes(); # для мягкого удаления
            $table->timestamps();
        });
    }
    public function down()  {
        $this->schema->drop('forum_categories');
    }
}
