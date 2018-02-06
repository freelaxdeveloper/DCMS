<?php

include_once '../sys/inc/start.php';
use App\{document,files,files_file,captcha,form};
use App\App\App;

$doc = new document(1); // инициализация документа для браузера
$doc->title = __('Мой аватар');

$avatar_file_name = App::user()->id . '.jpg';
$avatars_path = FILES . '/.avatars'; // папка с аватарами
$avatars_dir = new files($avatars_path);



if (!empty($_FILES ['file'])) {
    if ($_FILES ['file'] ['error']) {
        $doc->err(__('Ошибка при загрузке'));
    } elseif (!$_FILES ['file'] ['size']) {
        $doc->err(__('Содержимое файла пусто'));
    } elseif (!preg_match('#\.jpe?g$#ui', $_FILES ['file'] ['name'])) {
        $doc->err(__('Неверное расширение файла'));
    } elseif (!$img = @imagecreatefromjpeg($_FILES ['file'] ['tmp_name'])) {
        $doc->err(__('Файл не является изображением JPEG'));
    } elseif (@imagesx($img) < 128) {
        $doc->err(__('Ширина изображения должна быть не менее 128 px'));
    } elseif (@imagesy($img) < 128) {
        $doc->err(__('Высота изображения должна быть не менее 128 px'));
    } else {
        if ($avatars_dir->is_file($avatar_file_name)) {
            $avatar = new files_file($avatars_path, $avatar_file_name);
            $avatar->delete(); // удаляем старый аватар
        }

        if ($files_ok = $avatars_dir->filesAdd(array($_FILES ['file'] ['tmp_name'] => $avatar_file_name))) {
            $avatars_dir->group_show = 0;
            $files_ok [$_FILES ['file'] ['tmp_name']]->group_show = 0;
            $files_ok [$_FILES ['file'] ['tmp_name']]->id_user = App::user()->id;
            $files_ok [$_FILES ['file'] ['tmp_name']]->group_edit = max(App::user()->group, 2);

            unset($files_ok);
            $doc->msg(__('Аватар успешно установлен'));
        } else {
            $doc->err(__('Не удалось сохранить выгруженный файл'));
        }
    }
}

// Аватар 
if ($path = App::user()->getAvatar($doc->img_max_width())) {

    if (!empty($_POST ['delete'])) {
        $avatar = new files_file($avatars_path, $avatar_file_name);
        if (empty($_POST ['captcha']) || empty($_POST ['captcha_session']) || !captcha::check($_POST ['captcha'], $_POST ['captcha_session']))
            $doc->err(__('Проверочное число введено неверно'));
        elseif ($avatar->delete()) {
            $doc->msg(__('Аватар успешно удален'));

            $doc->ret(__('Мой аватар'), '?' . passgen());
            header('Refresh: 1; url=?' . passgen());
            exit;
        } else {

            $doc->err(__('Не удалось удалить аватар'));
        }
    }

    echo "<img class='photo' src='" . $path . "' alt='".__('Мой аватар')."' /><br />\n";

    $form = new form('?' . passgen());
    $form->captcha();
    $form->button(__('Удалить'), 'delete');
    $form->display();
}

$form = new form('?' . passgen());
$form->file('file', __('Файл аватара').' (*.jpg)');
$form->button(__('Выгрузить'));
$form->display();