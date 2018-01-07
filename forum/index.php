<?php

include_once '../sys/inc/start.php';
use App\{document,cache_counters,listing,pages,text};
use App\Models\{ForumCategory,ForumTheme,ForumMessage};

$doc = new document();
$doc->title = __('Форум - Категории');

$listing = new listing();

$post = $listing->post();
$post->url = 'search.php';
$post->title = __('Поиск');
$post->icon('forum.search');

if (false === ($new_themes = cache_counters::get('forum.new_themes.' . $user->group))) {
    $new_themes = ForumTheme::group($user)->where('time_create', '>', NEW_TIME)->count();
    cache_counters::set('forum.new_themes.' . $user->group, $new_themes, 20);
}
$post = $listing->post();
$post->url = 'last.themes.php';
$post->title = __('Новые темы');
if ($new_themes) {
    $post->counter = '+' . $new_themes;
}
$post->icon('forum.lt');

if (false === ($new_posts = cache_counters::get('forum.new_posts.' . $user->group))) {
    $new_posts = ForumMessage::group($user)->where('time', '>', NEW_TIME)->count();
    cache_counters::set('forum.new_posts.' . $user->group, $new_posts, 20);
}

$post = $listing->post();
$post->url = 'last.posts.php';
$post->title = __('Обновленные темы');
if ($new_posts) {
    $post->counter = '+' . $new_posts;
}
$post->icon('forum.lp');


if ($user->id) {
    if (false === ($my_themes = cache_counters::get('forum.my_themes.' . $user->id))) {
        # количество тем у которых есть новые сообщения не от автора
         $my_themes = ForumMessage::group($user)->where([
            ['time', '>', NEW_TIME],
            ['id_user', '<>', $user->id],
        ])->count();
        cache_counters::set('forum.my_themes.' . $user->id, $my_themes, 20);
    }


    $post = $listing->post();
    $post->url = 'my.themes.php';
    $post->title = __('Мои темы');
    if ($my_themes) {
        $post->counter = '+' . $my_themes;
    }
    $post->icon('forum.my_themes');
}

$pages = new pages();
$pages->posts = ForumCategory::group($user)->count();

$categories = ForumCategory::group($user)->get()->forPage($pages->this_page, $user->items_per_page);
foreach ($categories as $category) {
    $post = $listing->post();
    $post->url = "category.php?id={$category->id}";
    $post->title = text::toValue($category->name);
    $post->icon('forum.category');
    $post->post = text::for_opis($category->description);
}

$listing->display(__('Доступных Вам категорий нет'));

$pages->display('?'); // вывод страниц

if ($user->group >= 5) {
    $doc->act(__('Создать категорию'), 'category.new.php');
    $doc->act(__('Порядок категорий'), 'categories.sort.php');
    $doc->act(__('Статистика'), 'stat.php');
}