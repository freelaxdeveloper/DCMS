<?php

include_once '../sys/inc/start.php';
include 'inc/functions.php';
use App\{document,cache,listing,user,pages,text,misc};
use App\Models\ForumTheme;
use App\App\App;

$doc = new document();
$doc->title = __('Новые темы');

$cache_id = 'forum.last.themes_all';

// TODO подсвечивать непрочитанные темы
// TODO разделять темы по пунктам (сегодня | вчера | неделя)
if (false === ($themes = cache::get($cache_id))) {
    
    $themes = ForumTheme::lastThemes()
        ->withCount(['messages' => function ($query) {
            $query->group();
        }, 'views'])
        ->orderBy('updated_at', 'DESC')->get();

        cache::set($cache_id, $themes, 30);
}
$pages = new pages($themes->count());
$themes = $themes->forPage($pages->this_page, App::user()->items_per_page);

view('forum.themes', compact('themes'));
$pages->display('?');

$doc->ret(__('Форум'), './');