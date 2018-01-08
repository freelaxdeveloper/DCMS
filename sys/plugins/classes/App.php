<?php
namespace App;

abstract class App{

    public static function icon(?string $icon): string
    {
        $icon_path = '/sys/images/icons/' . basename($icon, '.png') . '.png';
        if (!is_file(H . $icon_path)) {
            return self::icon('info');
        }
        return $icon_path;
    }

    public static function user()
    {
        static $current_user;
        if (!$current_user) {
            global $user;
            return $user;    
        }
        return $current_user;
    }
}