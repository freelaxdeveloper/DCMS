<?php
if (App::user()->group){
    $menu = new menu_ini('user');
    $menu->display();
}
