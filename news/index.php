<?php

include_once '../sys/inc/start.php';
use App\{document,text,pages,listing,user,misc};
use App\Models\News;
use App\App\App;

$doc = new document();

$doc->title = __('Наши новости');

$pages = new pages(News::count());

$news = News::orderBy('id', 'desc')->get()->forPage($pages->this_page, App::user()->items_per_page);

view('news.news', compact('news'));

$pages->display('?'); // вывод страниц

if ($user->group >= 4) {
    $doc->act(__('Добавить новость'), 'news.add.php');
}