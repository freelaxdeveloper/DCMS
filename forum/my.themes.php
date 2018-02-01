<?php
include_once '../sys/inc/start.php';
use App\{document,pages,listing,text,misc};
use App\Models\{User,ForumTheme};
use App\App\App;

$doc = new document(1);
$doc->title = __('Мои темы');

$ank = (empty($_GET['id'])) ? App::user() : User::find((int) $_GET['id']);
if (!$ank->group)
    $doc->access_denied(__('Нет данных'));

$doc->title = ($ank->id == App::user()->id) ? __('Мои темы') : __('Темы пользователя "%s"', $ank->login);

$pages = new pages(ForumTheme::group()->where('id_autor', $ank->id)->count());
$themes = ForumTheme::whereHas('messages', function ($query) {
    $query->group();
})->withCount(['messages' => function ($query) {
    $query->group();
}, 'views'])->where('id_autor', $ank->id)->orderBy('id', 'desc')->get()->forPage($pages->this_page, App::user()->items_per_page);
view('forum.themes', compact('themes'));
$pages->display("?id={$ank->id}&amp;");

$doc->ret(__('Форум'), './');