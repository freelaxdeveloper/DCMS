<?php
namespace App\App;

use App\{dcms};
use App\Models\User;
use App\App\Authorize;

abstract class App{

    public static function icon(?string $icon): string
    {
        $icon_path = '/sys/images/icons/' . basename($icon, '.png') . '.png';
        if (!is_file(H . $icon_path)) {
            return self::icon('info');
        }
        return $icon_path;
    }
    # авторизация пользователя
    public static function user()
    {
        static $current_user;
        if (!$current_user && Authorize::isAuthorize()) {
            $current_user = User::where([
                'password' => Authorize::getHash(),
                'token' => Authorize::getId(),
            ])->first();
        } elseif (!$current_user) {
            $current_user = new User;
            $current_user->login = '[Гость]';
            $current_user->group = 0;
            $current_user->id = 0;
            $current_user->language = 'ru';
        }
        return $current_user;
    }
    
}