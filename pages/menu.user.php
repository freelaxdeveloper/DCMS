<?php
include_once '../sys/inc/start.php';
use App\{document,menu_ini};

$doc = new document(1);
$doc->title = __('Личное меню');

$menu = new menu_ini('user'); // загружаем пользовательское меню
$menu->display(); // выводим пользовательское меню