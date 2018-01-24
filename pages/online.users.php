<?php

include_once '../sys/inc/start.php';
use App\{document,pages,listing,text,misc};
use App\Models\{User,UserOnline};
use App\App\App;

$doc = new document();

$pages = new pages(UserOnline::count());

$doc->title = __('Сейчас на сайте (%s)', $pages->posts);

$listing = new listing();

if ($arr = UserOnline::get()->forPage($pages->this_page, App::user()->items_per_page)) {
    foreach ($arr AS $user) {
        $post = $listing->post();
        $post->title = $user->user->login;
        $post->url = '/profile.view.php?id=' . $user->user->id;
        $post->icon($user->user->icon);

        if (App::user()->id === $user->user->id || App::user()->group > $user->user->group) {
            $post->content[] = __('Браузер') . ': ' . text::toValue($user->browser->name);
            $post->content[] = __('IP-адрес') . ': ' . long2ip($user->ip_long);
        }

        $post->content[] = __('Переходов') . ': ' . $user->conversions;
        $post->content[] = __('Последний визит') . ': ' . misc::when($user->last_visit);
    }

}

$listing->display(__('Нет пользователей'));

$pages->display('?');