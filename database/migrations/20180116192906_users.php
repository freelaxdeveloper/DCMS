<?php

use \App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

class Users extends Migration
{
    public function up()  {
        $this->schema->create('users', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedSmallInteger('group')->default(1);
            $table->string('login');
            $table->string('password');
            $table->string('token');
            $table->string('a_code')->nullable()->comment('Код активации');
            $table->string('recovery_password')->nullable()->comment('Ключ для восстановления пароля');
            $table->enum('sex', ['0', '1'])->default(1)->comment('Пол');
            $table->integer('reg_date')->comment('Дата регистрации');
            // $table->timestamp('reg_date')->useCurrent()->comment('Дата регистрации');
            $table->string('reg_mail')->nullable()->comment('E-mail, указанный при регистрации');
            $table->integer('last_visit')->unsigned()->nullable()->comment('Последнее посещение');
            // $table->timestamp('last_visit')->useCurrent()->comment('Последнее посещение');
            $table->integer('count_visit')->default(1)->comment('Количество посещений');
            $table->integer('conversions')->default(1)->comment('Количество переходов');
            $table->unsignedSmallInteger('time_shift')->default(0)->comment('Сдвиг времени');
            $table->string('skype')->nullable()->comment('Skype логин');
            $table->string('email')->nullable()->comment('Email для анкеты');
            $table->string('lastname')->nullable()->comment('Фамилия');
            $table->string('realname')->nullable()->comment('Имя');
            $table->string('middle_n')->nullable()->comment('Отчество');
            $table->tinyInteger('ank_d_r')->default(0)->comment('День рождения');
            $table->tinyInteger('ank_m_r')->default(0)->comment('Месяц рождения');
            $table->unsignedSmallInteger('ank_g_r')->default(0)->comment('Год рождения');
            $table->integer('balls')->default(1)->unsigned()->comment('Баллы (показатель активности)');
            $table->integer('vip_time')->nullable()->comment('Время действия VIP статуса');
            // $table->timestamp('vip_time')->nullable()->comment('Время действия VIP статуса');
            $table->float('rating')->default(0)->comment('Рейтинг пользователя');
            $table->integer('mail_new_count')->default(0)->unsigned()->comment('Количество новых писем');
            $table->integer('friend_new_count')->default(0)->unsigned()->comment('Количество запросов дружбы');
            $table->enum('vis_email', ['0', '1'])->default(0)->comment('Показывать Email');
            $table->enum('vis_friends', ['0', '1'])->default(1)->comment('Отображение списка друзей');
            $table->enum('vis_skype', ['0', '1'])->default(1)->comment('отображение логина Skype');
            $table->mediumText('description')->nullable()->comment('О себе');
            $table->bigInteger('wmid')->default(0)->comment('WebMoney ID');
            $table->float('balance_rub')->default(0)->comment('Баланс: рубли');
            $table->enum('notice_mention', ['0', '1'])->default(1)->comment('уведомления об упоминании');
            $table->enum('notification_forum', ['0', '1'])->default(1)->comment('уведомление на форуме');
            $table->string('language')->nullable()->comment('Языковой пакет');
            $table->string('languages')->nullable()->comment('список языков');
            $table->enum('mail_only_friends', ['0', '1'])->default(0);
            $table->string('theme_light')->nullable()->comment('light тема');
            $table->string('theme_full')->nullable()->comment('full тема');
            $table->unsignedTinyInteger('items_per_page_light')->unsigned()->default(7)->comment('Количество пунктов на одну страницу');
            $table->unsignedTinyInteger('items_per_page_full')->unsigned()->default(30)->comment('Количество пунктов на одну страницу');
            $table->unsignedTinyInteger('items_per_page_mobile')->unsigned()->default(15)->comment('Количество пунктов на одну страницу');
            $table->string('theme_mobile')->nullable()->comment('тема для touch устройств');
            $table->integer('last_time_login')->nullable()->comment('Время посл.изменения логина');
            // $table->timestamp('last_time_login')->nullable()->comment('Время посл.изменения логина');

            $table->timestamps();

        });
    }
    public function down()  {
        $this->schema->drop('users');
    }
}
