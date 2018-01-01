<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = __('Форум');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Refresh: 1; url=./');
    $doc->err(__('Ошибка выбора темы'));
    exit;
}
$id_theme = (int)$_GET['id'];
$q = $db->prepare("SELECT `th`.* ,
 `cat`.`name` AS `category_name` ,
  `tp`.`name` AS `topic_name`
FROM `forum_themes` AS `th`
JOIN `forum_categories` AS `cat` ON `cat`.`id` = `th`.`id_category`
JOIN `forum_topics` AS `tp` ON `tp`.`id` = `th`.`id_topic`
WHERE `th`.`id` = :id_theme AND `th`.`group_show` <= :gr AND `tp`.`group_show` <= :gr AND `cat`.`group_show` <= :gr");
$q->execute(Array(':id_theme' => $id_theme, ':gr' => current_user::getInstance()->group));
if (!$theme = $q->fetch()) {
    header('Refresh: 1; url=./');
    $doc->err(__('Тема не доступна'));
    exit;
}

if ($user->group) {
    $q = $db->prepare("SELECT * FROM `forum_views` WHERE `id_theme` = ? AND `id_user` = ? AND `time` > ?");
    $q->execute(Array($theme['id'], $user->id, DAY_TIME));
    if (!$q->fetch()) {
        // если пользователь сегодня еще не заходил в тему, то добавляем запись
        $res = $db->prepare("INSERT INTO `forum_views` (`id_theme`, `id_user`, `time`) VALUES (?, ?, ?)");
        $res->execute(Array($theme['id'], $user->id, (TIME + 1)));
    } else {
        // если пользователь уже сегодня заходил в тему, то обновляем время у существующей записи
        $res = $db->prepare("UPDATE `forum_views` SET `time` = ? WHERE `id_theme` = ? AND `id_user` = ? ORDER BY `time` DESC LIMIT 1");
        $res->execute(Array((TIME + 1), $theme['id'], $user->id));
    }
}

$doc->title .= ' - ' . $theme['name'];

$doc->keywords[] = __('Форум');
$doc->keywords[] = $theme['name'];
$doc->keywords[] = $theme['topic_name'];
$doc->keywords[] = $theme['category_name'];

$res = $db->prepare("SELECT COUNT(*) FROM `forum_messages` WHERE `id_theme` = ? AND `group_show` <= ?");
$res->execute(Array($theme['id'], $user->group));
$pages = new pages;
$pages->posts = $res->fetchColumn();
$doc->description = __('Форум') . ' - ' . $theme['name'] . ' - ' . __('Страница %s из %s', $pages->this_page, $pages->pages);

include 'inc/theme.votes.php';


$img_thumb_down = '<a href="{url}" class="DCMS_thumb_down ' . implode(' ', sprite::getClassName('thumb_down', SPRITE_CLASS_PREFIX)) . '"></a>';
$img_thumb_up = '<a href="{url}" href="" class="DCMS_thumb_up ' . implode(' ', sprite::getClassName('thumb_up', SPRITE_CLASS_PREFIX)) . '"></a>';

$q = $db->prepare("SELECT * FROM `forum_messages` WHERE `id_theme` = ? AND `group_show` <= ? ORDER BY `id` ASC LIMIT " . $pages->limit);
$q->execute(Array($theme['id'], $user->group));
$users_preload = array();
$messages = array();
$msg_ids = array();
while ($message = $q->fetch()) {
    $msg_ids[] = $message['id'];
    $messages[] = $message;
    $users_preload[] = $message['id_user'];
}

new user($users_preload); // предзагрузка данных пользователей одним запросом

$ratings = array();
if ($user->group) {
    $q = $db->prepare("SELECT * FROM `forum_rating` WHERE `id_user` = :id_user AND `id_message` IN (" . implode(',', $msg_ids) . ")");
    $q->execute(array(':id_user' => $user->id));
    $forum_rating_result = $q->fetchAll();
    foreach ($forum_rating_result AS $rating) {
        $ratings[$rating['id_message']] = $rating['rating'];
    }
}

$listing = new listing();
foreach ($messages AS $message) {
    $post = $listing->post();
    $post->id = 'message' . $message['id'];
    $ank = new user((int)$message['id_user']);

    if ($user->group) {
        $post->action('quote', "message.php?id_message=$message[id]&amp;quote"); // цитирование
    }

    if ($user->group > $ank->group || ($user->id && $user->id == $theme['id_moderator']) || $user->group == groups::max()) {
        if ($theme['group_show'] <= 1) {
            if ($message['group_show'] <= 1) {
                $post->action('hide', "message.edit.php?id=$message[id]&amp;return=" . URL . "&amp;act=hide&amp;" . passgen()); // скрытие
            } else {
                $post->action('show', "message.edit.php?id=$message[id]&amp;return=" . URL . "&amp;act=show&amp;" . passgen()); // показ

                $post->bottom = __('Сообщение скрыто');
            }
        }
        $post->action('edit', "message.edit.php?id=$message[id]&amp;return=" . URL); // редактирование
    } elseif ($user->id == $message['id_user'] && TIME < $message['time'] + 600) {
        // автору сообщения разрешается его редактировать в течении 10 минут
        $post->action('edit', "message.edit.php?id=$message[id]&amp;return=" . URL); // редактирование
    }

    if ($ank->group <= $user->group && $user->id != $ank->id) {
        if ($user->group >= 2)
            // бан
            $post->action('complaint', "/dpanel/user.ban.php?id_ank=$message[id_user]&amp;return=" . URL . "&amp;link=" . urlencode("/forum/message.php?id_message=$message[id]"));
        else
            // жалоба на сообщение
            $post->action('complaint', "/complaint.php?id=$message[id_user]&amp;return=" . URL . "&amp;link=" . urlencode("/forum/message.php?id_message=$message[id]"));
    }

    $post->title = $ank->nick();
    $post->icon($ank->icon());

    $doc->last_modified = $message['time'];
    $post->time = misc::when($message['time']);
    $post->url = 'message.php?id_message=' . $message['id'];
    $post->content = text::for_opis($message['message']);

    if ($message['edit_id_user'] && ($ank->group < $user->group || $ank->id == $user->id)) {
        $ank_edit = new user($message['edit_id_user']);
        $post->bottom .= ' <a href="message.history.php?id=' . $message['id'] . '&amp;return=' . URL . '">' . __('Изменено') . '(' . $message['edit_count'] . ')</a> ' . $ank_edit->login . ' (' . misc::when($message['edit_time']) . ')<br />';
    }

    if ($user->group && $user->id != $ank->id) {
        $my_rating = array_key_exists($message['id'], $ratings) ? $ratings[$message['id']] : 0;
        if ($my_rating === 0 && $user->balls - $dcms->forum_rating_down_balls >= 0) {
            $post->bottom .= str_replace('{url}', 'message.rating.php?id=' . $message['id'] . '&amp;change=down&amp;return=' . URL . urlencode('#' . $post->id), $img_thumb_down);
        }


        if ($my_rating === 0) {
            $post->bottom .= ' ' . __('Рейтинг: %s / %s', '<span class="DCMS_rating_down">' . $message['rating_down'] . '</span>', '<span class="DCMS_rating_up">' . $message['rating_up'] . '</span>') . ' ';
        } else {
            $post->bottom .= ' ' . __('Рейтинг: %s / %s / %s', '<span class="DCMS_rating_down">' . $message['rating_down'] . '</span>', $my_rating, '<span class="DCMS_rating_up">' . $message['rating_up'] . '</span>') . ' ';
        }


        if ($my_rating === 0)
            $post->bottom .= str_replace('{url}', 'message.rating.php?id=' . $message['id'] . '&amp;change=up&amp;return=' . URL . urlencode('#' . $post->id), $img_thumb_up);
    } else {
        $post->bottom .= ' ' . __('Рейтинг: %s / %s', '<span class="DCMS_rating_down">' . $message['rating_down'] . '</span>', '<span class="DCMS_rating_up">' . $message['rating_up'] . '</span>') . ' ';
    }

    $post_dir_path = H . '/sys/files/.forum/' . $theme['id'] . '/' . $message['id'];
    if (@is_dir($post_dir_path)) {
        $listing_files = new listing();
        $dir = new files($post_dir_path);
        $content = $dir->getList('time_add:asc');
        $files = &$content['files'];
        $count = count($files);
        for ($i = 0; $i < $count; $i++) {
            $file = $listing_files->post();
            $file->title = text::toValue($files[$i]->runame);
            $file->url = "/files" . $files[$i]->getPath() . ".htm?order=time_add:asc";
            $file->content[] = $files[$i]->properties;
            $file->icon($files[$i]->icon());
            $file->image = $files[$i]->image();
        }
        if ($count){
            $post->content .= $listing_files->fetch();
        }
    }
}

$listing->display(__('Сообщения отсутствуют'));

$pages->display('theme.php?id=' . $theme['id'] . '&amp;'); // вывод страниц

if ($theme['group_write'] <= $user->group) {
    $doc->act(__('Написать сообщение'), 'message.new.php?id_theme=' . $theme['id'] . "&amp;return=" . URL);
}

if ($user->group >= 2 || $theme['group_edit'] <= $user->group || ($user->id && $user->id == $theme['id_moderator'])) {
    $doc->act(__('Действия'), 'theme.actions.php?id=' . $theme['id']);
}

$doc->ret($theme['topic_name'], 'topic.php?id=' . $theme['id_topic']);
$doc->ret($theme['category_name'], 'category.php?id=' . $theme['id_category']);
$doc->ret(__('Форум'), './');