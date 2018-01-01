<?php

include_once '../sys/inc/start.php';
$doc = new document(4);
$doc->title = __('Редактирование новости');
$doc->ret(__('К новостям'), './');

$id = (int) @$_GET['id'];

$q = $db->prepare("SELECT * FROM `news` WHERE `id` = ? LIMIT 1");
$q->execute(Array($id));

if (!$news = $q->fetch())
    $doc->access_denied(__('Новость не найдена или удалена'));


$ank = new user($news['id_user']);

if ($ank->group > $user->group)
    $doc->access_denied(__('У Вас нет прав для редактирования данной новости'));

$news_e = &$_SESSION['news_edit'][$id];

if (isset($_POST['clear']))
    $news_e = array();

if (empty($news_e)) {
    $news_e = array();
    $news_e['title'] = $news['title'];
    $news_e['text'] = $news['text'];
    $news_e['checked'] = false;
}

if ($news_e['checked'] && isset($_POST['send'])) {
    if (empty($_POST['captcha']) || empty($_POST['captcha_session']) || !captcha::check($_POST['captcha'], $_POST['captcha_session']))
        $doc->err(__('Ошибка при вводе чисел с картинки'));
    else {
        $res = $db->prepare("UPDATE `news` SET `title` = ?, `id_user` = ?, `text` = ?, `sended` = '0' WHERE `id` = ? LIMIT 1");
        $res->execute(Array($news_e['title'], $user->id, $news_e['text'], $id));
        $doc->msg(__('Новость успешно отредактирована'));
        $news_e = array();
        header('Refresh: 1; ./');
        exit;
    }
}

if (isset($_POST['edit']))
    $news['checked'] = 0;

if (isset($_POST['next'])) {
    $title = text::for_name($_POST['title']);
    $text = text::input_text($_POST['text']);

    if (!$title)
        $doc->err(__('Заполните "Заголовок новости"'));
    else
        $news_e['title'] = $title;
    if (!$text)
        $doc->err(__('Заполните "Текст новости"'));
    else
        $news_e['text'] = $text;

    if ($title && $text)
        $news_e['checked'] = 1;
}

$form = new form(new url());
$form->text('title', __('Заголовок новости'), $news_e['title'], true, false, $news_e['checked']);
$form->textarea('text', __('Текст новости'), $news_e['text'], true, $news_e['checked']);

if ($news_e['checked']) {
    $form->captcha();
    $form->button(__('Редактировать'), 'edit', false);
    $form->button(__('Применить'), 'send', false);
} else {
    $form->button(__('Очистить'), 'clear', false);
    $form->button(__('Далее'), 'next', false);
}

$form->display();
