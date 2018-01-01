<?php

include_once '../sys/inc/start.php';
$doc = new document(4);
$doc->title = __('Удаление новости');
$doc->ret(__('К новостям'), './');

$id = (int)@$_GET['id'];

$q = $db->prepare("SELECT * FROM `news` WHERE `id` = ? LIMIT 1");
$q->execute(Array($id));
if (!$news = $q->fetch())
    $doc->access_denied(__('Новость не найдена или уже удалена'));


$ank = new user($news['id_user']);

if ($ank->group > $user->group)
    $doc->access_denied(__('У Вас нет прав для удаления данной новости'));

if (isset($_POST['delete'])) {
    if (empty($_POST['captcha']) || empty($_POST['captcha_session']) || !captcha::check($_POST['captcha'], $_POST['captcha_session'])) {
        $doc->err(__('Проверочное число введено неверно'));
    } else {
        $res = $db->prepare("DELETE FROM `news` WHERE `id` = ? LIMIT 1");
        $res->execute(Array($id));
        $res = $db->prepare("DELETE FROM `news_comments` WHERE `id_news` = ?");
        $res->execute(Array($id));
        $doc->msg(__('Новость успешно удалена'));
        header('Refresh: 1; url=./');
        exit;
    }
}

$form = new form(new url());
$form->captcha();
$form->bbcode(__('Новость "%s" будет удалена без возможности восстановления', $news['title']));
$form->button(__('Удалить'), 'delete');
$form->display();
