<?php
namespace App\App;

use App\{crypt,dcms};
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

    /* public static function user(bool $model = false)
    {
        static $current_user;
        if (!$current_user) {
            global $user;
            return $model ? User::find($user->id) : $user;
        }
        return $model ? User::find($current_user->id) : $current_user;
    } */
    # авторизация пользователя
    public static function user()
    {
        static $current_user;
        if (!$current_user && Authorize::isAuthorize()) {
            $current_user = User::find(Authorize::getId());
            /* if ($current_user->id && $current_user->token_time_update < TIME) {
                $current_user->updateToken();
            } */
            # если почему-то хэш пользователя не совпадает с тем что в сессии
            # сбрасываем авторизацию
            $hash_password = crypt::decrypt(Authorize::getHash(), dcms::getInstance()->salt_user);
            if ($current_user->id && $current_user->password != $hash_password) {
                Authorize::exit();
                die('Ошибка авторизации');
                //self::access_denied(__('Ошибка авторизации'), true);
            }
        } elseif (!$current_user) {
            $current_user = new User;
            $current_user->login = '[Гость]';
            $current_user->group = 0;
            $current_user->id = 0;
        }
        return $current_user;
    }
    
}