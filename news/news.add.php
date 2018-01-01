<?php

include_once '../sys/inc/start.php';
$doc = new document(4);
$doc->title = __('Создание новости');
$doc->ret(__('К новостям'), './');
$news = & $_SESSION['news_create'];

if (isset($_POST['clear'])) $news = array();

if (empty($news)) {
    $news = array();
    $news['title'] = '';
    $news['text'] = '';
    $news['checked'] = false;
}

if ($news['checked'] && isset($_POST['send'])) {
    if (empty($_POST['captcha']) || empty($_POST['captcha_session']) || !captcha::check($_POST['captcha'], $_POST['captcha_session']))
        $doc->err(__('Ошибка при вводе чисел с картинки'));
    else {
        $res = $db->prepare("INSERT INTO `news` (`title`, `time`, `text`, `id_user`) VALUES (?,?,?,?)");
        $res->execute(Array($news['title'], TIME, $news['text'], $user->id));
        $doc->msg(__('Новость успешно опубликована'));
        $news = array();
        header('Refresh: 1; ./');
        exit;
    }
}

if (isset($_POST['edit'])) $news['checked'] = 0;

if (isset($_POST['next'])) {
    $title = text::for_name($_POST['title']);
    $text = text::input_text($_POST['text']);

    if (!$title) $doc->err(__('Заполните "Заголовок новости"'));
    else $news['title'] = $title;
    if (!$text) $doc->err(__('Заполните "Текст новости"'));
    else $news['text'] = $text;

    if ($title && $text) $news['checked'] = 1;
}

$form = new form('?' . passgen());
$form->text('title', __('Заголовок новости'), $news['title'], true, false, $news['checked']);
$form->textarea('text', __('Текст новости'), $news['text'], true, $news['checked']);
if ($news['checked']) {
    $form->captcha();
    $form->button(__('Редактировать'), 'edit', false);
    $form->button(__('Опубликовать'), 'send', false);
} else {
    $form->button(__('Очистить'), 'clear', false);
    $form->button(__('Далее'), 'next', false);
}
$form->display();