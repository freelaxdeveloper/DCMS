<?php
defined('DCMS') or die;
use App\{files,files_file,listing,user,misc,form,url,antiflood,pages,text,design};
use App\App\App;

$pathinfo = pathinfo($abs_path);
$dir = new files($pathinfo['dirname']);

if ($dir->group_show > App::user()->group) {
    $doc->access_denied(__('У Вас нет прав для просмотра файлов в данной папке'));
}
$access_write_dir = $dir->group_write <= App::user()->group || ($dir->id_user && App::user()->id == $dir->id_user);

$order_keys = $dir->getKeys();
if (!empty($_GET['order']) && isset($order_keys[$_GET['order']])) $order = $_GET['order'];
else $order = 'runame:asc';

$file = new files_file($pathinfo['dirname'], $pathinfo['basename']);

if ($file->group_show > App::user()->group) $doc->access_denied(__('У Вас нет прав для просмотра данного файла'));

$access_edit = $file->group_edit <= App::user()->group || ($file->id_user && $file->id_user == App::user()->id);

if ($access_edit && isset($_GET['act']) && $_GET['act'] == 'edit_screens') include 'inc/screens_edit.php';


$doc->title = __('Файл %s - скачать', $file->runame);
$doc->description = $file->meta_description ? $file->meta_description : $dir->meta_description;
$doc->keywords = $file->meta_keywords ? explode(',', $file->meta_keywords) : ($dir->meta_keywords ? explode(',',
    $dir->meta_keywords) : '');

if ($access_edit) include 'inc/file_act.php';

if (App::user()->group && $file->id_user != App::user()->id && isset($_POST['rating'])) {
    $my_rating = (int)$_POST['rating'];
    if (isset($file->ratings[$my_rating])) {
        $file->rating_my($my_rating);
        $doc->msg(__('Ваша оценка файла успешно принята'));

        header('Refresh: 1; url=?order=' . $order . '&' . passgen() . SID);
        $doc->ret(__('Вернуться'), '?order=' . $order . '&amp;' . passgen());
        exit;
    } else {
        $doc->err(__('Нет такой оценки файла'));
    }
}

if (empty($_GET['act'])) {
    $screens_count = $file->getScreensCount();
    $query_screen = (int)@$_GET['screen_num'];
    if ($screens_count) {
        if ($query_screen < 0 || $query_screen >= $screens_count) $query_screen = 0;

        if ($screen = $file->getScreen($doc->img_max_width(), $query_screen)) {
            echo "<img class='DCMS_photo' src='" . $screen . "' alt='" . __('Скриншот') . " $query_screen' /><br />\n";
        }

        if ($screens_count > 1) {
            $select = array();

            for ($i = 0; $i < $screens_count; $i++) {
                $select[] = array('?order=' . $order . '&amp;screen_num=' . $i, $i + 1, $query_screen == $i);
            }

            $show = new design();
            $show->assign('select', $select);
            $show->display('design.select_bar.tpl');
        }
    }

    $listing = new listing();

    if ($description = $file->description) {
        $post = $listing->post();
        $post->title = __('Описание');
        $post->icon('info');
        $post->content[] = $description;
    }

    if ($title = $file->title) {
        $post = $listing->post();
        $post->title = __('Заголовок');
        $post->content[] = $title;
    }

    if ($artist = $file->artist) {
        $post = $listing->post();
        $post->title = __('Исполнители');
        $post->content[] = $artist;
        $doc->keywords[] = $artist;
    }

    if ($band = $file->band) {
        $post = $listing->post();
        $post->title = __('Группа');
        $post->content[] = $band;
        $doc->keywords[] = $band;
    }

    if ($album = $file->album) {
        $post = $listing->post();
        $post->title = __('Альбом');
        $post->content[] = $album;
        $doc->keywords[] = $album;
    }

    if ($year = $file->year) {
        $post = $listing->post();
        $post->title = __('Год');
        $post->content[] = $year;
    }

    if ($genre = $file->genre) {
        $post = $listing->post();
        $post->title = __('Жанр');
        $post->content[] = $genre;
    }

    if ($comment = $file->comment) {
        $post = $listing->post();
        $post->title = __('Комментарий');
        $post->content[] = $comment;
    }

    if ($track_number = (int)$file->track_number) {
        $post = $listing->post();
        $post->title = __('Номер трека');
        $post->content[] = $track_number;
    }

    if ($language = $file->language) {
        $post = $listing->post();
        $post->title = __('Язык');
        $post->content[] = $language;
    }

    if ($url = $file->url) {
        $post = $listing->post();
        $post->title = __('Ссылка');
        $post->content[] = $url;
    }

    if ($copyright = $file->copyright) {
        $post = $listing->post();
        $post->title = __('Копирайт');
        $post->content[] = $copyright;
    }

    if ($vendor = $file->vendor) {
        $post = $listing->post();
        $post->title = __('Производитель');
        $post->content[] = $vendor;
    }

    if (($width = (int)$file->width) && ($height = (int)$file->height)) {
        $post = $listing->post();
        $post->title = __('Разрешение');
        $post->content[] = $width . 'x' . $height;
    }

    if ($frames = (int)$file->frames) {
        $post = $listing->post();
        $post->title = __('Кол-во кадров');
        $post->content[] = $frames;
    }

    if ($playtime_string = $file->playtime_string) {
        $post = $listing->post();
        $post->title = __('Продолжительность');
        $post->content[] = $playtime_string;
    }

    if (($video_bitrate = (int)$file->video_bitrate) && ($video_bitrate_mode = $file->video_bitrate_mode)) {
        $post = $listing->post();
        $post->title = __('Видео битрейт');
        $post->content[] = misc::getDataCapacity($video_bitrate) . "/s (" . $video_bitrate_mode . ")";
    }

    if ($video_codec = $file->video_codec) {
        $post = $listing->post();
        $post->title = __('Видео кодек');
        $post->content[] = $video_codec;
    }

    if ($video_frame_rate = $file->video_frame_rate) {
        $post = $listing->post();
        $post->title = __('Частота');
        $post->content[] = __('%s кадров в секунду', round($video_frame_rate / 60));
    }

    if (($audio_bitrate = (int)$file->audio_bitrate) && ($audio_bitrate_mode = $file->audio_bitrate_mode)) {
        $post = $listing->post();
        $post->title = __('Аудио битрейт');
        $post->content[] = misc::getDataCapacity($audio_bitrate) . "/s (" . $audio_bitrate_mode . ")";
    }

    if ($audio_codec = $file->audio_codec) {
        $post = $listing->post();
        $post->title = __('Аудио кодек');
        $post->content[] = $audio_codec;
    }

    if ($file->id_user) {
        $ank = new user($file->id_user);

        $post = $listing->post();
        $post->title = __('Файл загрузил' . ($ank->sex ? '' : 'а'));

        $post->content = $ank->nick;
        $post->url = '/profile.view.php?id=' . $ank->id;
        $post->time = misc::when($file->time_add);
    }

    $post = $listing->post();
    $post->title = __('Кол-во скачиваний');
    $post->content[] = intval($file->downloads) . ' ' . __(misc::number($file->downloads, 'раз', 'раза', 'раз'));

    $post = $listing->post();
    $post->title = __('Размер файла');
    $post->content[] = misc::getDataCapacity($file->size);

    $post = $listing->post();
    $post->title = __('Общая оценка');
    $post->content[] = $file->rating_name . ' (' . round($file->rating, 1) . '/' . $file->rating_count . ")";

    $listing->display();


    if (App::user()->group && $file->id_user != App::user()->id) {
        $my_rating = $file->rating_my(); // мой рейтинг
        $form = new design();
        $form->assign('method', 'post');
        $form->assign('action', '?order=' . $order . '&amp;screen_num=' . $query_screen . '&amp;' . passgen());
        $elements = array();
        $options = array();
        foreach ($file->ratings AS $rating => $rating_name) {
            $options[] = array($rating, $rating_name, $rating == $my_rating);
        }
        $elements[] = array('type' => 'select', 'title' => __('Оценка файла'), 'br' => 1, 'info' => array('name' => 'rating',
            'options' => $options));
        $elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'save', 'value' => __('Оценить'))); // кнопка
        $form->assign('el', $elements);
        $form->display('input.form.tpl');
    }

    $form = new form();
    $form->text('url', __('Скопировать ссылку'), 'http://' . $_SERVER['HTTP_HOST'] . '/files' . $file->getPath());
    $form->display();

    $form = new form('/files' . $file->getPath());
    $form->hidden('rnd', passgen());
    $form->button(__('Скачать %s', $file->name));
    $form->display();
}

$can_write = true;
if (!App::user()->is_writeable) {
    $doc->msg(__('Писать запрещено'), 'write_denied');
    $can_write = false;
}

//region комменты к файлу
if ($can_write && isset($_POST['send']) && isset($_POST['message']) && isset($_POST['token']) && App::user()->group) {
    $message = (string)$_POST['message'];
    $users_in_message = text::nickSearch($message);
    $message = text::input_text($message);

    if (!antiflood::useToken($_POST['token'], 'files')) {
        // повторная отправка формы
        // вывод сообщений, возможно, будет лишним
    } else if ($file->id_user && $file->id_user != App::user()->id && (empty($_POST['captcha']) || empty($_POST['captcha_session'])
            || !captcha::check($_POST['captcha'], $_POST['captcha_session']))
    ) {
        $doc->err(__('Проверочное число введено неверно'));
    } elseif ($dcms->censure && $mat = is_valid::mat($message)) {
        $doc->err(__('Обнаружен мат: %s', $mat));
    } elseif ($message) {
        App::user()->balls += $dcms->add_balls_comment_file;
        $res = $db->prepare("INSERT INTO `files_comments` (`id_file`, `id_user`, `time`, `text`) VALUES (?,?,?,?)");
        $res->execute(Array($file->id, App::user()->id, TIME, $message));
        $doc->msg(__('Комментарий успешно оставлен'));

        $id_message = $db->lastInsertId();
        if ($users_in_message) {
            for ($i = 0; $i < count($users_in_message) && $i < 20; $i++) {
                $user_id_in_message = $users_in_message[$i];
                if ($user_id_in_message == App::user()->id || ($file->id_user && $file->id_user == $user_id_in_message)) {
                    continue;
                }
                $ank_in_message = new user($user_id_in_message);
                if ($ank_in_message->notice_mention) {
                    $ank_in_message->mess("[user]{App::user()->id}[/user] упомянул" . (App::user()->sex ? '' : 'а') . " о Вас в комментарии к файлу [url=/files{$file->getPath()}.htm#comment{$id_message}]$file->runame[/url]");
                }
            }
        }

        $file->comments++;

        if ($file->id_user && $file->id_user != App::user()->id) { // уведомляем автора о комментарии
            $ank = new user($file->id_user);
            $ank->mess("[user]{App::user()->id}[/user] оставил" . (App::user()->sex ? '' : 'а') . " комментарий к Вашему файлу [url=/files{$file->getPath()}.htm]$file->runame[/url]");
        }
    } else {
        $doc->err(__('Комментарий пуст'));
    }
}

if (empty($_GET['act'])) {
    // комменты будут отображаться только когда над файлом не производится никаких действий
    if ($can_write && App::user()->group) {
        $form = new form(new url(null, array('screen_num' => $query_screen)));
        $form->textarea('message', __('Комментарий'));
        if ($file->id_user && $file->id_user != App::user()->id) $form->captcha();
        $form->hidden('token', antiflood::getToken('files'));
        $form->button(__('Отправить'), 'send');
        $form->display();
    }

    if (!empty($_GET['delete_comm']) && App::user()->group >= $file->group_edit) {
        $delete_comm = (int)$_GET['delete_comm'];
        $res = $db->prepare("SELECT COUNT(*) FROM `files_comments` WHERE `id` = ? AND `id_file` = ?");
        $res->execute(Array($delete_comm, $file->id));
        $k = $res->fetchColumn();
        if ($k) {
            $res = $db->prepare("DELETE FROM `files_comments` WHERE `id` = ? LIMIT 1");
            $res->execute(Array($delete_comm));
            $file->comments--;
            $doc->msg(__('Комментарий успешно удален'));
        } else $doc->err(__('Комментарий уже удален'));
    }

    //$posts = array();
    $listing = new listing();
    $pages = new pages;
    $res = $db->prepare("SELECT COUNT(*) FROM `files_comments` WHERE `id_file` = ?");
    $res->execute(Array($file->id));
    $pages->posts = $res->fetchColumn();

    $q = $db->prepare("SELECT * FROM `files_comments` WHERE `id_file` = ? ORDER BY `id` DESC LIMIT " . $pages->limit);
    $q->execute(Array($file->id));
    if ($arr = $q->fetchAll()) {
        foreach ($arr AS $comment) {

            $ank = new user($comment['id_user']);

            $post = $listing->post();
            $post->url = '/profile.view.php?id=' . $ank->id;
            $post->title = $ank->nick();
            $post->time = misc::when($comment['time']);
            $post->post = text::toOutput($comment['text']);
            $post->icon($ank->icon());

            if (App::user()->group >= $file->group_edit) {
                $post->action('delete',
                    '?order=' . $order . '&amp;screen_num=' . $query_screen . '&amp;delete_comm=' . $comment['id']);
            }
        }
    }
    $listing->display(__('Комментарии отсутствуют'));

    $pages->display('?order=' . $order . '&amp;screen_num=' . $query_screen . '&amp;'); // вывод страниц
}
//endregion
// переход к рядом лежащим файлам в папке
$content = $dir->getList($order);
$files = &$content['files'];
$count = count($files);

if ($count > 1) {
    for ($i = 0; $i < $count; $i++) {
        if ($file->name == $files[$i]->name) $fileindex = $i;
    }

    if (isset($fileindex)) {
        $select = array();

        if ($fileindex >= 1) {
            $last_index = $fileindex - 1;
            $select[] = array('./' . urlencode($files[$last_index]->name) . '.htm?order=' . $order, text::toValue($files[$last_index]->runame));
        }

        $select[] = array('?order=' . $order, text::toValue($file->runame), true);

        if ($fileindex < $count - 1) {
            $next_index = $fileindex + 1;
            $select[] = array('./' . urlencode($files[$next_index]->name) . '.htm?order=' . $order, text::toValue($files[$next_index]->runame));
        }

        $show = new design();
        $show->assign('select', $select);
        $show->display('design.select_bar.tpl');
    }
}

$doc->ret($dir->runame, './?order=' . $order); // возвращение в папку
$return = $dir->ret(5); // последние 5 ссылок пути
for ($i = 0; $i < count($return); $i++) {
    $doc->ret($return[$i]['runame'], '/files' . $return[$i]['path']);
}

if ($access_edit) include 'inc/file_form.php';
exit;
