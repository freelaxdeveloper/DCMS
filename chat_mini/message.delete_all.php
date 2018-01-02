<?php

include_once '../sys/inc/start.php';
use App\{document,form,captcha};
use App\Models\Chat_mini;

$doc = new document(3);
$doc->title = __('Удаление сообщений');

if (isset($_POST['delete'])) {
    if (empty($_POST['captcha']) || empty($_POST['captcha_session']) || !captcha::check($_POST['captcha'], $_POST['captcha_session'])) {
        $doc->err(__('Проверочное число введено неверно'));
    } else {
        $dcms->log('Мини чат', 'Очистка от всех сообщений');

        Chat_mini::truncate();
        $doc->msg(__('Все сообщения успешно удалены'));
        $doc->toReturn('./');
        $doc->ret(__('Вернуться'), './');
        exit;
    }
}

$form = new form('?' . passgen());
$form->captcha();
$form->bbcode('* '.__('Все сообщения будут удалены без возможности восстановления'));
$form->button(__('Удалить'), "delete");
$form->display();

$doc->ret(__('Вернуться'), './');