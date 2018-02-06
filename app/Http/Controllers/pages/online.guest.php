<?php

include_once '../sys/inc/start.php';
use App\{document,pages,listing};
use App\Models\GuestOnline;
use App\App\App;

$bots = isset($_GET['bots']) ? '1' : '0';

$doc = new document;
$pages = new pages(GuestOnline::when($bots, function ($query) use ($bots) {
    return $query->where('is_robot', $bots);
})->where('conversions', '>=', 3)->count());

$doc->tab(__('Роботы'), '?bots', $bots);
$doc->tab(__('Гости'), '?', !$bots);

$guests = GuestOnline::when($bots, function ($query) use ($bots) {
    return $query->where('is_robot', $bots);
})->where('conversions', '>=', 3)->get();

view('pages.online_guest', compact('guests'));

$doc->title = $bots ? __('Роботы на сайте (%s)', $pages->posts) : __('Гости на сайте (%s)', $pages->posts);

$pages->display('?' . ($bots ? 'bots' : '') . '&amp;');