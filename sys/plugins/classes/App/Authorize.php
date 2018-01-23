<?php
namespace App\App;

use App\Models\User;
use App\App\Crypt;

abstract class Authorize{
    const KEY_TOKEN = 'session_token';
    const KEY_ID = 'session_id';

    # авторизируемся
    public static function authorized(User $user)
    {
        if (self::isAuthorize()) {
            return;
        }
        $_SESSION[self::KEY_TOKEN] = $user->token;
        $_SESSION[self::KEY_ID] = Crypt::encrypt($user->id);
        setcookie(self::KEY_TOKEN, $user->token, TIME + 3600 * 24 * 365, '/');
        setcookie(self::KEY_ID, Crypt::encrypt($user->id), TIME + 3600 * 24 * 365, '/');
    }
    # проверям авторизованы ли
    public static function isAuthorize(): bool
    {
        return self::getId() ? true : false;
    }
    # получаем ID пользователя
    public static function getId(): string
    {
        $id = $_SESSION[self::KEY_ID] ?? $_COOKIE[self::KEY_ID] ?? '';
        return Crypt::decrypt($id);
    }
    # получаем токен пользователя
    public static function getToken(): string
    {
        return $_SESSION[self::KEY_TOKEN] ?? $_COOKIE[self::KEY_TOKEN] ?? '';
    }

    # выходим с авторизации
    public static function exit()
    {
        unset($_SESSION[self::KEY_TOKEN]);
        unset($_SESSION[self::KEY_ID]);
        setcookie(self::KEY_TOKEN, null, null, '/');
        setcookie(self::KEY_ID, null, null, '/');
    }
}
