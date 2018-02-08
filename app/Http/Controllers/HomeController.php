<?php
namespace Dcms\Http\Controllers;

use Dcms\Core\Controller;
use App\{widget,ini};

class HomeController extends Controller{

    public function index()
    {
        $widgets = (array)ini::read(H . '/sys/ini/widgets.ini'); // получаем список виджетов

        foreach ($widgets as $widget_name => $show) {
            if (!$show) {
                continue; // если стоит отметка о скрытии, то пропускаем
            }
            $widget = new widget(H . '/sys/widgets/' . $widget_name); // открываем
            $widget->display(); // отображаем
        }
    }
}