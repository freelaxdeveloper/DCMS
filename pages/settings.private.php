<?php

include_once '../sys/inc/start.php';
$doc = new document(1);
$doc->title = __('Настройки приватности');

if (isset($_POST ['save'])) {
    $user->vis_email = !empty($_POST ['email']);
    $user->vis_icq = !empty($_POST ['icq']);
    $user->vis_friends = !empty($_POST ['friends']);
    $user->vis_skype = !empty($_POST ['skype']);
    $user->mail_only_friends = !empty($_POST ['mail_only_friends']);
    $doc->msg(__('Параметры успешно сохранены'));
}

$form = new form('?' . passgen());
$form->checkbox('email', __('Показывать %s', 'E-Mail'), $user->vis_email);
$form->checkbox('icq', __('Показывать %s', 'ICQ'), $user->vis_icq);
$form->checkbox('skype', __('Показывать %s', 'Skype'), $user->vis_skype);
$form->checkbox('friends', __('Список друзей'), $user->vis_friends);
$form->bbcode(__('Ваши друзья будут видеть все ваши данные независимо от установленных параметров'));
$form->checkbox('mail_only_friends', __('Принимать личные сообщения только от друзей'), $user->mail_only_friends);
$form->button(__('Применить'), 'save');
$form->display();

$doc->ret(__('Личное меню'), '/menu.user.php');