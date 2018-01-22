<?php
namespace App;

use App\{DB,ApiAuthRequiredException,api_controller};
use App\App\App;

/**
 * Класс для работы со списком друзей
 * Class api_friends
 */
abstract class api_friends implements api_controller
{

    /**
     * получение списка друзей
     * @param mixed $request_data
     * @throws ApiAuthRequiredException
     * @return mixed
     */
    public static function get($request_data)
    {
        if (!App::user()->id)
            throw new ApiAuthRequiredException($request_data);

        $q = DB::me()->prepare("SELECT * FROM `friends` WHERE `id_user` = ? ORDER BY `confirm` ASC, `time` DESC");
        $q->execute(Array(App::user()->id));
        return $q->fetchAll();
    }

}