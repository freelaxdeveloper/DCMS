<?php
namespace App;

use App\Models\User;

/**
 * Работа с данными текущего пользователя
 * Class api_user
 */
class api_user implements api_controller
{
    /**
     * Получение запрошенных полей пользователя
     * @param $request_data string[] Список полей, которые необходимо получить
     * @return array
     */
    public static function get($request_data)
    {
        return App::user()->get()->toArray();
    }

} 