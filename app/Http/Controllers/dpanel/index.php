<?php
include_once '../sys/inc/start.php';
use App\{document, menu_ini};

$doc = new document(2);
$doc->title = __('Панель управления DCMS');
$menu = new menu_ini('dpanel'); // загружаем меню dPanel
$menu->display(); // выводим меню dPanel