<?php
include_once '../sys/inc/start.php';
$doc = new document ();
$doc->title = __('Фотоальбомы');

if (!empty($_GET ['id'])) {
    $ank = new user((int) $_GET ['id']);
} else {
    $ank = $user;
}

if (!$ank->group) {
    $doc->access_denied(__('Ошибка пользователя'));
}

// папка фотоальбомов пользователей
$photos = new files(FILES . '/.photos');
// папка альбомов пользователя
$albums_path = FILES . '/.photos/' . $ank->id;

if (!@is_dir($albums_path)) {
    if (!$albums_dir = $photos->mkdir($ank->login, $ank->id))
            $doc->access_denied(__('Не удалось создать папку под фотоальбомы пользователя'));

    $albums_dir->id_user = $ank->id;
    $albums_dir->group_show = 0;
    $albums_dir->group_write = min($ank->group, 2);
    $albums_dir->group_edit = max($ank->group, 4);
    unset($albums_dir);
}

$albums_dir = new files($albums_path);

if (empty($_GET ['album']) || !$albums_dir->is_dir($_GET ['album'])) {
    $doc->err(__('Запрошеный альбом не существует'));
    $doc->ret(__('К альбомам'), 'albums.php?id=' . $ank->id);
    header('Refresh: 1; url=albums.php?id=' . $ank->id);
    exit();
}

$album_name = (string) $_GET ['album'];
$album = new files($albums_path . '/' . $album_name);
$doc->title = $album->runame;

if (empty($_GET ['photo']) || !$album->is_file($_GET ['photo'])) {
    $doc->err(__('Запрошенная фотография не найдена'));
    $doc->ret(__('К альбому %s', $name), 'photos.php?id=' . $ank->id . '&amp;album=' . urlencode($album->name));
    $doc->ret(__('К альбомам'), 'albums.php?id=' . $ank->id);
    header('Refresh: 1; url=photos.php?id=' . $ank->id . '&alnum=' . urlencode($album->name));
    exit();
}

$photo_name = $_GET ['photo'];

$photo = new files_file($albums_path . '/' . $album_name, $photo_name);
$doc->title = $photo->runame;

$doc->description = __('Фото пользователя %s:%s', $ank->login, $photo->runame);
$doc->keywords [] = $photo->runame;
$doc->keywords [] = $album->runame;
$doc->keywords [] = $ank->login;

// удаление фотографии
if ($photo->id_user && $photo->id_user == $user->id) {
    if (!empty($_GET ['act']) && $_GET ['act'] === 'delete') {

        if (!empty($_POST ['delete'])) {
            if (empty($_POST ['captcha']) || empty($_POST ['captcha_session']) || !captcha::check($_POST ['captcha'],
                    $_POST ['captcha_session'])) $doc->err(__('Проверочное число введено неверно'));
            elseif ($photo->delete()) {
                $doc->msg(__('Фото успешно удалено'));
                $doc->ret(__('Альбом %s', $album->name),
                    'photos.php?id=' . $ank->id . '&amp;album=' . urlencode($album->name));
                $doc->ret(__('Альбомы %s', $ank->nick), 'albums.php?id=' . $ank->id);
                header('Refresh: 1; url=photos.php?id=' . $ank->id . '&album=' . urlencode($album->name) . '&' . passgen());
                exit();
            } else {

                $doc->err(__('Не удалось удалить фото'));
                $doc->ret(__('К фото'),
                    '?id=' . $ank->id . '&amp;album=' . urlencode($album->name) . '&amp;photo=' . urlencode($photo->name));
                $doc->ret(__('Альбом %s', $album->name),
                    'photos.php?id=' . $ank->id . '&amp;album=' . urlencode($album->name));
                $doc->ret(__('Альбомы %s', $ank->login), 'albums.php?id=' . $ank->id);
                header('Refresh: 1; url=?id=' . $ank->id . '&album=' . urlencode($album->name) . '&photo=' . urlencode($photo->name) . '&' . passgen());
            }
            exit();
        }

        $form = new form(new url(null, array('id' => $ank->id, 'album' => $album->name, 'photo' => $photo->name)));
        $form->captcha();
        $form->button(__('Удалить фото'), 'delete');
        $form->display();

        $doc->ret(__('К фото'),
            '?id=' . $ank->id . '&amp;album=' . urlencode($album->name) . '&amp;photo=' . urlencode($photo->name));
        $doc->ret(__('Альбом %s', $album->name), 'photos.php?id=' . $ank->id . '&amp;album=' . urlencode($album->name));
        $doc->ret(__('Альбомы %s', $ank->login), 'albums.php?id=' . $ank->id);
        exit();
    }

    $doc->act(__('Удалить фото'),
        '?id=' . $ank->id . '&amp;album=' . urlencode($album->name) . '&amp;photo=' . urlencode($photo->name) . '&amp;act=delete');
}

if ($screen = $photo->getScreen($doc->img_max_width(), 0)) {
    echo "<img class='DCMS_photo' src='" . $screen . "' alt='" . __('Фото') . " " . text::toValue($photo->runame) . "' /><br />\n";
}

$can_write = true;
if (!$user->is_writeable) {
    $doc->err(__('Новым пользователям разрешено писать только через %s часа пребывания на сайте',
            $dcms->user_write_limit_hour));
    $can_write = false;
}

if ($can_write) {


// добавление комментария
    if (isset($_POST ['send']) && isset($_POST ['message']) && $user->group) {
        $message = text::input_text($_POST ['message']);

        if ($photo->id_user && $photo->id_user != $user->id && (empty($_POST ['captcha']) || empty($_POST ['captcha_session'])
            || !captcha::check($_POST ['captcha'], $_POST ['captcha_session'])))
                $doc->err(__('Проверочное число введено неверно'));
        elseif ($dcms->censure && $mat = is_valid::mat($message)) $doc->err(__('Обнаружен мат: %s', $mat));
        elseif ($message) {
            $user->balls++;
            $res = $db->prepare("INSERT INTO `files_comments` (`id_file`, `id_user`, `time`, `text`) VALUES (?,?,?,?)");
            $res->execute(Array($photo->id, $user->id, TIME, $message));
            $doc->msg(__('Комментарий успешно оставлен'));
            $photo->comments++;

            if ($photo->id_user && $photo->id_user != $user->id) { // уведомляем автора о комментарии
                $ank->mess("$user->login оставил" . ($user->sex ? '' : 'а') . " комментарий к Вашему фото [url=/photos/photo.php?id=$ank->id&album=$album->name&photo=$photo->name]{$photo->runame}[/url]");
            }
        } else {
            $doc->err(__('Комментарий пуст'));
        }
    }

// форма добавления комментария
    if ($user->group) {
        $form = new form(new url(null, array('id' => $ank->id, 'album' => $album->name, 'photo' => $photo)));
        $form->textarea('message', __('Комментарий'));
        if ($photo->id_user && $photo->id_user != $user->id) $form->captcha();
        $form->button(__('Отправить'), 'send');
        $form->display();
    }
}
if (!empty($_GET ['delete_comm']) && $user->group >= $photo->group_edit) {
    $delete_comm = (int) $_GET ['delete_comm'];
    $res = $db->prepare("SELECT COUNT(*) FROM `files_comments` WHERE `id` = ? AND `id_file` = ? LIMIT 1");
    $res->execute(Array($delete_comm, $photo->id));
    $k = $res->fetchColumn();
    if ($k) {
        $res = $db->prepare("DELETE FROM `files_comments` WHERE `id` = ? LIMIT 1");
        $res->execute(Array($delete_comm));
        $photo->comments--;
        $doc->msg(__('Комментарий успешно удален'));
    } else $doc->err(__('Комментарий уже удален'));
}

$pages = new pages ();
$res = $db->prepare("SELECT COUNT(*) FROM `files_comments` WHERE `id_file` = ?");
$res->execute(Array($photo->id));
$pages->posts = $res->fetchColumn(); // количество сообщений
$q = $db->prepare("SELECT * FROM `files_comments` WHERE `id_file` = ? ORDER BY `id` DESC LIMIT " . $pages->limit);
$q->execute(Array($photo->id));

$listing = new listing();
if ($arr = $q->fetchAll()) {
    foreach ($arr AS $comment) {
        $ank2 = new user($comment ['id_user']);
        $post = $listing->post();

        $post->title = $ank2->nick();
        $post->time = misc::when($comment ['time']);
        $post->icon($ank2->icon());
        $post->content = text::toOutput($comment ['text']);

        if ($user->group >= $photo->group_edit) {
            $post->action('delete',
                '?id=' . $ank->id . '&amp;album=' . urlencode($album->name) . '&amp;photo=' . urlencode($photo->name) . '&amp;delete_comm=' . $comment ['id']);
        }
    }
}
$listing->display(__('Комментарии отсутствуют'));

$pages->display('?id=' . $ank->id . '&amp;album=' . urlencode($album->name) . '&amp;photo=' . urlencode($photo->name) . '&amp;'); // вывод страниц


$doc->ret(__('Альбом %s', $album->runame), 'photos.php?id=' . $ank->id . '&amp;album=' . urlencode($album->name));
$doc->ret(__('Альбомы %s', $ank->login), 'albums.php?id=' . $ank->id);
