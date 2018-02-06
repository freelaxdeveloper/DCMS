<?php
namespace App\App;

use App\{dcms};
use Dcms\Models\User;
use App\App\Authorize;

abstract class App{

    public static function icon(?string $icon): string
    {
        $icon_path = '/images/icons/' . basename($icon, '.png') . '.png';
        if (!is_file(H . '/public/' . $icon_path)) {
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
                'id' => Authorize::getId(),
                'token' => Authorize::getToken(),
            ])->first();
            if (!$current_user) {
                Authorize::exit();
                redirect();
            }
        } elseif (!$current_user) {
            $current_user = new User;
            $current_user->login = '[Гость]';
            $current_user->group = 0;
            $current_user->id = 0;
            $current_user->language = 'ru';
        }
        return $current_user;
    }
    /**
     * генерация токена
     */
    public static function generateToken(int $lenght = 32): string {
        return bin2hex(random_bytes($lenght));
    }
    
    public static function http_auth()
    {
        if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
            throw new \Exception('No access!');
        }
        if (USER !== $_SERVER['PHP_AUTH_USER'] || PASS !== $_SERVER['PHP_AUTH_PW']) {
            throw new \Exception('No access!');
        }    
    }

    public static function getURI(): string
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'], '/');
        }
    }
    # получаем язык сайта из адресной строки
    public static function current_language(): string
    {
        $language_current = explode('/', self::getURI())[0];
        return $language_current;
    }

}