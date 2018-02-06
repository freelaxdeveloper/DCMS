<?php

include_once '../sys/inc/start.php';
use App\{document,form};
use App\App\App;

$doc = new document(1);
$doc->title = __('Настройки уведомлений');

if (isset($_POST ['save'])) {
    App::user()->notice_mention = !empty($_POST ['notice_mention']);
    App::user()->notification_forum = !empty($_POST ['notification_forum']);
    $doc->msg(__('Параметры успешно сохранены'));
}

$form = new form('?' . passgen());
$form->checkbox('notice_mention', __('Упоминание ника (@%s)', App::user()->login), App::user()->notice_mention);
$form->checkbox('notification_forum', __('Ответ на форуме'), App::user()->notification_forum);
$form->button(__('Применить'), 'save');
$form->display();

$doc->ret(__('Личное меню'), '/menu.user.php');