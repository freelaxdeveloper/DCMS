<?php
include_once '../sys/inc/start.php';
use App\{document,listing,user,text,misc};
use App\Models\ChatMini;
use App\App\App;

$doc = new document();
$doc->title = __('Действия');

if (!isset($_GET ['id']) || !is_numeric($_GET ['id'])) {
    $doc->toReturn('./');
    $doc->err(__('Ошибка выбора сообщения'));
    exit();
}
$id_message = (int) $_GET ['id'];

if (!$message = ChatMini::find($id_message)) {
    $doc->toReturn('./');
    $doc->err(__('Сообщение не найдено'));
    exit();
}

$listing = new listing;

$ank = new user($message->id_user);

$post = $listing->post();
$post->title = $ank->nick();
$post->content = text::toOutput($message->message);
$post->time = misc::when($message->time);
$post->icon($ank->icon());

$post = $listing->post();
$post->title = __('Посмотреть анкету');
$post->icon('ank_view');
$post->url = '/profile.view.php?id=' . $ank->id;


if (App::user()->group) {
    $post = $listing->post();
    $post->title = __('Ответить');
    $post->icon('reply');
    $post->url = 'index.php?message=' . $id_message . '&amp;reply';

    $post = $listing->post();
    $post->title = __('Цитировать');
    $post->icon('quote');
    $post->url = 'index.php?message=' . $id_message . '&amp;quote';
}

if (App::user()->group >= 2) {
    $post = $listing->post();
    $post->title = __('Удалить сообщение');
    $post->icon('delete');
    $post->url = 'message.delete.php?id=' . $id_message;
}


$listing->display();


$doc->ret(__('Вернуться'), './');