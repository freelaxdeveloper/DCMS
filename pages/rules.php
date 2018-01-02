<?php

include_once '../sys/inc/start.php';
use App\{document,bb};

$doc = new document();
$doc->title = __('Правила сайта');
$doc->act(__('Кодекс'), '/code.php');
$doc->ret(__('Личное меню'), '/menu.user.php');

$bb = new bb(H . '/sys/docs/rules.txt');

if ($bb->title)
    $doc->title = $bb->title;

$bb->display();
