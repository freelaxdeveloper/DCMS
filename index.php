<?php
include_once 'sys/inc/start.php';
use App\{document,widget,ini};
file_put_contents(H . '/test.txt', date("Y-m-d H:i:s"));

$doc = new document;
$doc->title = __($dcms->title); // локализированое название сайта
$widgets = (array)ini::read(H . '/sys/ini/widgets.ini'); // получаем список виджетов

foreach ($widgets as $widget_name => $show) {
    if (!$show) {
        continue; // если стоит отметка о скрытии, то пропускаем
    }
    $widget = new widget(H . '/sys/widgets/' . $widget_name); // открываем
    $widget->display(); // отображаем
}
