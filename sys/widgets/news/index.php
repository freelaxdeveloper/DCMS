<?php
use App\{listing,DB,text,misc};
use App\Models\News;

defined('DCMS') or die;

$listing = new listing();
$post = $listing->post();
$post->highlight = true;
$post->icon('news');
$post->url = '/news/';
$post->title = __('Все новости');

if ($dcms->widget_items_count) {
    $week = mktime(0, 0, 0, date('n'), -7);
    $listNews = News::where('time', '>', $week)->orderBy('id', 'DESC')->take($dcms->widget_items_count)->get();
    foreach ($listNews as $news) {
        $post = $listing->post();
        $post->icon('news');
        $post->title = text::toValue($news->title);
        $post->url = '/news/comments.php?id=' . $news->id;
        $post->time = misc::when($news->time);
        $post->highlight = $news->time > NEW_TIME;
    }
}

$listing->display();
