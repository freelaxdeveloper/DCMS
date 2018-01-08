<?php
namespace App;

use App\Models\User;

abstract class App{

    public static function icon(?string $icon): string
    {
        $icon_path = '/sys/images/icons/' . basename($icon, '.png') . '.png';
        if (!is_file(H . $icon_path)) {
            return self::icon('info');
        }
        return $icon_path;
    }

    public static function user(bool $model = false)
    {
        static $current_user;
        if (!$current_user) {
            global $user;
            return $model ? User::find($user->id) : $user;
        }
        return $model ? User::find($current_user->id) : $current_user;
    }
}