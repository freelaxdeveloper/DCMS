<?php

include_once '../sys/inc/start.php';
use App\{document,form};
use App\App\App;

$doc = new document(1);
$doc->title = __('Настройки приватности');

if (isset($_POST ['save'])) {
    App::user()->vis_email = !empty($_POST ['email']);
    App::user()->vis_icq = !empty($_POST ['icq']);
    App::user()->vis_friends = !empty($_POST ['friends']);
    App::user()->vis_skype = !empty($_POST ['skype']);
    App::user()->mail_only_friends = !empty($_POST ['mail_only_friends']);
    $doc->msg(__('Параметры успешно сохранены'));
}

$form = new form('?' . passgen());
$form->checkbox('email', __('Показывать %s', 'E-Mail'), App::user()->vis_email);
$form->checkbox('icq', __('Показывать %s', 'ICQ'), App::user()->vis_icq);
$form->checkbox('skype', __('Показывать %s', 'Skype'), App::user()->vis_skype);
$form->checkbox('friends', __('Список друзей'), App::user()->vis_friends);
$form->bbcode(__('Ваши друзья будут видеть все ваши данные независимо от установленных параметров'));
$form->checkbox('mail_only_friends', __('Принимать личные сообщения только от друзей'), App::user()->mail_only_friends);
$form->button(__('Применить'), 'save');
$form->display();

$doc->ret(__('Личное меню'), '/menu.user.php');