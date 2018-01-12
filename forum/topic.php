<?php
include_once '../sys/inc/start.php';

use App\{document,pages,listing,text,misc,current_user,user};
use \App\Models\{ForumTopic,ForumMessage,ForumTheme};

$doc = new document();

$doc->title = __('Форум');
if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Refresh: 1; url=./');
    $doc->err(__('Ошибка выбора раздела'));

    exit;
}
$id_top = (int)$_GET['id'];

if (!$topic = ForumTopic::group($user)->find($id_top)){
    header('Refresh: 1; url=./');
    $doc->err(__('Раздел не доступен'));
    exit;
}

$doc->title = $topic->name;

$pages = new pages;
$pages->posts = ForumTheme::group($user)->where('id_topic', $topic->id)->count();

/**
 * если в теме есть сообщения группа чтения которого выше чем у пользователя, такую тему не выводим
 */
/*
$themes = ForumTheme::whereDoesntHave('messages', function ($query) use ($user) {
    $query->where('group_show', '>', $user->group);
})->orderByRaw('top ASC', 'time_last ASC')->get();
*/

/**
 * если в теме нет сообщений доступных для чтения, такую тему не выводим
 */
$themes = ForumTheme::whereHas('messages', function ($query) use ($user) {
    $query->group($user);
})->withCount(['messages' => function ($query) use ($user) {
    $query->group($user);
}, 'views'])->orderByRaw('top ASC', 'time_last ASC')->get()->forPage($pages->this_page, $user->items_per_page);

view('forum.themes', compact('themes'));

$pages->display('./topic.php?id=' . $topic->id . '&amp;'); // вывод страниц

if ($topic['group_write'] <= $user->group) {
    $doc->act(__('Начать новую тему'), './theme.new.php?id_topic=' . $topic->id . "&amp;return=" . URL);
}

if ($topic['group_edit'] <= $user->group) {
    $doc->act(__('Параметры раздела'), './topic.edit.php?id=' . $topic->id . "&amp;return=" . URL);
}

$doc->ret($topic->category->name, './category.php?id=' . $topic->id_category);
$doc->ret(__('Форум'), './');