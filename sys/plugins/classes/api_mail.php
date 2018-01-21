<?php
namespace App;

use App\{api_controller,current_user,ApiAuthRequiredException,ApiException,DB,text};
use App\App\App;

/**
 * Класс для работы с сообщеними пользователя
 * Class api_mail
 */
class api_mail implements api_controller
{
    public static function get($request_data)
    {
        if (!App::user()->id)
            throw new ApiAuthRequiredException($request_data);

        $ank = new user((int)@$request_data['id_user']);
        if (!$ank->id)
            throw new ApiException($request_data, __("Не указан контакт (id_user)"));

        // только непрочитанные
        $only_unreaded = !empty($request_data['only_unreaded']);
        // отмечать все письма как прочитанные
        $set_readed = !empty($request_data['set_readed']);
        // с выбранного времени
        $time_from = (int)@$request_data['time_from'];

        // начало списка
        $offset = !isset($request_data['offset']) ? 0 : (int)@$request_data['offset'];
        // кол-во писем
        $count = !isset($request_data['count']) ? 30 : (int)@$request_data['count'];

        if ($set_readed) {
            // отмечаем письма от этого человека как прочитанные
            $res = DB::me()->prepare("UPDATE `mail` SET `is_read` = '1' WHERE `id_user` = ? AND `id_sender` = ?");
            $res->execute(Array(App::user()->id, $ank->id));
        }

        $mail = array();
        $q = DB::me()->prepare("SELECT * FROM `mail` WHERE `time` > :time_from " . ($only_unreaded ? ' AND `is_read` = 0' : '') . " AND ((`id_user` = :id_user AND `id_sender` = :id_ank) OR (`id_user` = :id_ank AND `id_sender` = :id_user)) ORDER BY `id` DESC LIMIT $offset, $count");
        $q->execute(Array(
            ':time_from' => $time_from,
            ':id_user' => App::user()->id,
            ':id_ank' => $ank->id
        ));
        while ($m = $q->fetch()) {
            $mail[] = array('id' => (int)$m['id'],
                'id_sender' => (int)$m['id_sender'],
                'mess' => text::toOutput($m['mess']),
                'time' => (int)$m['time'],
                'is_read' => (bool)$m['is_read']
            );
        }
        return $mail;
    }
} 