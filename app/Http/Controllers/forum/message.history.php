<?php

include_once '../sys/inc/start.php';
use App\{document,pages,listing,user,text,misc};
use App\Models\{ForumMessage,ForumHistory};
use App\App\App;

$doc = new document();
$doc->title = __('История сообщений');
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Refresh: 1; url=./');
    $doc->err(__('Ошибка выбора сообщения'));
    exit;
}
$id_message = (int)$_GET['id'];

if (!$message = ForumMessage::group()->find($id_message)) {
    header('Refresh: 1; url=./');
    $doc->err(__('Сообщение не доступно'));
    exit;
}

if ($message->id_user != App::user()->id && $message->user->group >= App::user()->group) {
    header('Refresh: 1; url=./');
    $doc->err(__('Нет доступа к данной странице'));
    exit;
}

$listing = new listing();
$pages = new pages(ForumHistory::where('id_message', $message->id)->count());

$post = $listing->post();
$post->title = $message->user->login;
$post->icon($message->user->icon);
$post->content = text::toOutput($message->message);
$post->time = misc::when($message->edit_time ?? $message->time);
$post->bottom = __('Текущая версия');

if ($message->edit_id_user) {
    $post->bottom .= text::toOutput(' ([user]' . $message->edit_id_user . '[/user])');
}

$histories = ForumHistory::where('id_message', $message->id)
    ->get()->forPage($pages->this_page, App::user()->items_per_page);
foreach ($histories AS $history) {
    $post = $listing->post();
    $post->title = $history->user->login;
    $post->icon($history->user->icon);
    $post->content = $history->message;
    $post->time = misc::when($history->time);

    if ($history->id_user != $message->id_user) {
        $post->bottom = text::toOutput('[user]' . $history->id_user . '[/user]');
    }
}
$listing->display(__('Сообщения отсутствуют'));

$pages->display('?id=' . $message->id . '&amp;' . (isset($_GET['return']) ? 'return=' . urlencode($_GET['return']) . '&amp;' : null)); // вывод страниц

if (isset($_GET['return']))
    $doc->ret(__('В тему'), text::toValue($_GET['return']));
else
    $doc->ret(__('В тему'), 'theme.php?id=' . $message->id_theme);