<?php
/**
 * Анкета пользователя.
 * В данном файле используются регионы (region).
 * Для корректной работы с ними рекомендую использовать PhpStorm
 */

include_once '../sys/inc/start.php';
$doc = new document ();
$doc->title = __('Анкета');

$ank = (empty($_GET ['id'])) ? $user : new user((int)$_GET ['id']);

if(!$ank->group)
    $doc->access_denied(__('Нет данных'));

$doc->title = ($user->id && $ank->id == $user->id)? __('Моя анкета') : __('Анкета "%s"', $ank->nick);

$doc->description = __('Анкета "%s"', $ank->nick);
$doc->keywords [] = $ank->login;

//region Предложение дружбы
if ($user->group && $ank->id && $user->id != $ank->id && isset($_GET ['friend'])) {
    // обработка действий с "другом"
    $q = $db->prepare("SELECT * FROM `friends` WHERE `id_user` = ? AND `id_friend` = ? LIMIT 1");
    $q->execute(Array($user->id, $ank->id));
    if ($friend = $q->fetch()) {
        if ($friend ['confirm']) {

            // если Вы уже являетель другом
            if (isset($_POST ['delete'])) {
                // удаляем пользователя из друзей
                $res = $db->prepare("DELETE FROM `friends` WHERE `id_user` = ? AND `id_friend` = ? OR `id_user` = ? AND `id_friend` = ?");
                $res->execute(Array($user->id, $ank->id, $ank->id, $user->id));
                $doc->msg(__('Пользователь успешно удален из друзей'));
            }
        } else {
            // если не являетесь другом
            if (isset($_POST ['no'])) {
                // не принимаем предложение дружбы
                $res = $db->prepare("DELETE FROM `friends` WHERE `id_user` = ? AND `id_friend` = ? OR `id_user` = ? AND `id_friend` = ?");
                $res->execute(Array($user->id, $ank->id, $ank->id, $user->id));
                $res = $db->prepare("UPDATE `users` SET `friend_new_count` = `friend_new_count` - '1' WHERE `id` = ? LIMIT 1");
                $res->execute(Array($user->id));

                $doc->msg(__('Предложение дружбы отклонено'));
            } elseif (isset($_POST ['ok'])) {
                // принимаем предложение дружбы
                $res = $db->prepare("UPDATE `friends` SET `confirm` = '1' WHERE `id_user` = ? AND `id_friend` = ? LIMIT 1");
                $res->execute(Array($user->id, $ank->id));
                $res = $db->prepare("UPDATE `users` SET `friend_new_count` = `friend_new_count` - '1' WHERE `id` = ? LIMIT 1");
                $res->execute(Array($user->id));
                // на всякий случай пытаемся добавить поле (хотя оно уже должно быть), если оно уже есть, то дублироваться не будет
                $res = $db->prepare("INSERT INTO `friends` (`confirm`, `id_user`, `id_friend`) VALUES ('1', ?, ?)");
                $res->execute(Array($ank->id, $user->id));
                $doc->msg(__('Предложение дружбы принято'));
            }
        }
    } else {
        if (isset($_GET ['friend']) && isset($_POST ['add'])) {
            // предлагаем дружбу
            // отметка о запросе дружбы
            $res = $db->prepare("INSERT INTO `friends` (`confirm`, `id_user`, `id_friend`) VALUES ('0', ?, ?), ('1', ?, ?)");
            $res->execute(Array($ank->id, $user->id, $user->id, $ank->id));
            $res = $db->prepare("UPDATE `users` SET `friend_new_count` = `friend_new_count` + '1' WHERE `id` = ? LIMIT 1");
            $res->execute(Array($ank->id));

            $doc->msg(__('Предложение дружбы успешно отправлено'));
        }
    }
}

if ($user->group && $ank->id && $user->id != $ank->id) {
    $q = $db->prepare("SELECT * FROM `friends` WHERE `id_user` = ? AND `id_friend` = ? LIMIT 1");
    $q->execute(Array($user->id, $ank->id));
    if ($friend = $q->fetch()) {
        if ($friend ['confirm']) {
            // пользователь находится в друзьях
            if (isset($_GET ['friend']) && $_GET ['friend'] == 'delete') {
                $form = new form("?id={$ank->id}&amp;friend&amp;" . passgen());
                $form->bbcode(__('Действительно хотите удалить пользователя "%s" из друзей?', $ank->login));
                $form->button(__('Да, удалить'), 'delete');
                $form->display();
            }

            if (!$ank->is_friend($user))
                echo "<b>" . __('Пользователь еще не подтвердил факт Вашей дружбы') . "</b><br />";
            $doc->act(__('Удалить из друзей'), "?id={$ank->id}&amp;friend=delete");
        } else {
            // пользователь не в друзьях
            $form = new form("?id={$ank->id}&amp;friend&amp;" . passgen());
            $form->bbcode(__('Пользователь "%s" предлагает Вам дружбу', $ank->login));
            $form->button(__('Принимаю'), 'ok', false);
            $form->button(__('Не принимаю'), 'no', false);
            $form->display();
        }
    } else {
        if (isset($_GET ['friend']) && $_GET ['friend'] == 'add') {
            $form = new form("?id={$ank->id}&amp;friend&amp;" . passgen());
            $form->bbcode(__('Предложить пользователю "%s" дружбу?', $ank->login));
            $form->button(__('Предложить'), 'add', false);
            $form->display();
        }
        $doc->act(__('Добавить в друзья'), "?id={$ank->id}&amp;friend=add");
    }
}
//endregion

//region Бан
if ($ank->is_ban) {
    $ban_listing = new listing();

    $q = $db->prepare("SELECT * FROM `ban` WHERE `id_user` = ? AND `time_start` < ? AND (`time_end` is NULL OR `time_end` > ?) ORDER BY `id` DESC");
    $q->execute(Array($ank->id, TIME, TIME));
    if ($arr = $q->fetchAll()) {
        foreach ($arr AS $c) {
            $post = $ban_listing->post();
            $adm = new user($c ['id_adm']);

            $post->title = ($adm->group <= $user->group ? '<a href="/profile.view.php?id=' . $adm->id . '">' . $adm->nick . '</a>: ' : '') . text::toValue($c ['code']);


            if ($c ['time_start'] && TIME < $c ['time_start']) {
                $post->content[] = '[b]' . __('Начало действия') . ':[/b]' . misc::when($c ['time_start']) . "\n";
            }
            if ($c['time_end'] === NULL) {
                $post->content[] = '[b]' . __('Пожизненная блокировка') . "[/b]\n";
            } elseif (TIME < $c['time_end']) {
                $post->content[] = __('Осталось: %s', misc::when($c['time_end'])) . "\n";
            }
            if ($c['link']) {
                $post->content[] = __('Ссылка на нарушение: %s', $c['link']) . "\n";
            }

            $post->content[] = __('Комментарий: %s', $c['comment']) . "\n";
        }
    }
    $ban_listing->display();
}
//endregion

//region Профиль пользователя
$listing = new listing();
//region Аватар
if ($path = $ank->getAvatar($doc->img_max_width())) {
    echo "<img class='DCMS_photo' src='" . $path . "' alt='" . __('Аватар %s', $ank->login) . "' /><br />\n";
}
//endregion

//region статус
if ($ank->group > 1) {
    $post = $listing->post();
    $post->title = $ank->group_name;
    $post->highlight = true;
    $post->icon($ank->icon());

    //echo "<b>$ank->group_name</b>";

    $q = $db->prepare("SELECT `id_adm` FROM `log_of_user_status` WHERE `id_user` = ? ORDER BY `id` DESC LIMIT 1");
    $q->execute(Array($ank->id));
    if ($row = $q->fetch()) {
        $adm = new user($row['id_adm']);
        $post->content = __('Назначил' . ($adm->sex ? '' : 'а')) . ' ' . $adm->group_name . ' "' . $adm->nick . '"';

    }
} elseif ($ank->is_vip) {
    $post = $listing->post();
    $post->title = 'VIP';
    $post->url = '/faq.php?info=vip&amp;return=' . URL;
    $post->icon('vip');
}
//endregion

//region Пожертвования
if ($ank->donate_rub) {
    $post = $listing->post();
    $post->highlight = true;
    $post->icon('donate');
    $post->title = __('Пожертвования');
    $post->content = __('%s руб.', $ank->donate_rub);
    $post->url = '/users.php?order=donate_rub';
}
//endregion

//region Имя
if ($ank->realname) {
    /* ФИО, если заполнено фамилия, имя и отчество, 
       Вася Пупкин или Василий Петрович, если два из 3-х, 
       Или-же просто имя, если фамилия и отчество пусты. */
    $name = ($ank->lastname && $ank->middle_n) ? "$ank->lastname $ank->realname $ank->middle_n": $ank->realname . ($ank->middle_n ? " " . $ank->middle_n:'') . ($ank->lastname ? " " . $ank->lastname:'');
    $post = $listing->post();
    $post->title = ($ank->lastname && $ank->middle_n) ? __('ФИО') : __('Имя');
    $post->content[] = $name;
}
//endregion

//region Дата рождения
if ($ank->ank_d_r && $ank->ank_m_r && $ank->ank_g_r) {
    $post = $listing->post();
    $post->title = __('Дата рождения');
    $post->content = $ank->ank_d_r . ' ' . misc::getLocaleMonth($ank->ank_m_r) . ' ' . $ank->ank_g_r;

    $post = $listing->post();
    $post->title = __('Возраст');
    $post->content = misc::get_age($ank->ank_g_r, $ank->ank_m_r, $ank->ank_d_r, true);
} elseif ($ank->ank_d_r && $ank->ank_m_r) {

    $post = $listing->post();
    $post->title = __('День рождения');
    $post->content = $ank->ank_d_r . ' ' . misc::getLocaleMonth($ank->ank_m_r);
}
//endregion

//region фотографии
if ($ank->id) {

// папка фотоальбомов пользователей
    $photos = new files(FILES . '/.photos');
// папка альбомов пользователя
    $albums_path = FILES . '/.photos/' . $ank->id;

    if (!is_dir($albums_path)) {
        if ($albums_dir = $photos->mkdir($ank->login, $ank->id)) {
            $albums_dir->group_show = 0;
            $albums_dir->group_write = min($ank->group, 2);
            $albums_dir->group_edit = max($ank->group, 4);
            $albums_dir->id_user = $ank->id;
            unset($albums_dir);
        }
    }

    $albums_dir = new files($albums_path);

    $photos_count ['all'] = $albums_dir->count();
    if ($photos_count ['all']) {
        $photos_count ['new'] = $albums_dir->count(NEW_TIME);

        $post = $listing->post();
        $post->title = __('Фотографии');
        $post->icon('photos');
        $post->url = '/photos/albums.php?id=' . $ank->id;
        if ($photos_count ['new'])
            $post->counter = '+' . $photos_count ['new'];
    }
}
//endregion

//region язык(и)
if ($ank->language || $ank->languages) { // $ank->language(s)
    $post = $listing->post(); // Новый блок
    $post->icon('language'); // Пиктограмка пункта: иероглиф
    $post->title = ($ank->languages) ? __('Языки') : __('Язык'); 
    $post->content = $ank->languages ? $ank->languages : $ank->language; }
//endregion

//region аська
if ($ank->icq_uin) {
    if ($ank->is_friend($user) || $ank->vis_icq) {
        $post = $listing->post();
        $post->title = 'ICQ UIN';
        $post->content = $ank->icq_uin;
        $post->icon = 'http://wwp.icq.com/scripts/online.dll?icq=' . $ank->icq_uin . '&amp;img=27';
    } else {

        $post = $listing->post();
        $post->title = 'ICQ UIN';
        $post->url = '/faq.php?info=hide&amp;return=' . URL;
        $post->content = __('Информация скрыта');
    }
}
//endregion

//region Skype
if ($ank->skype) {
    if ($ank->is_friend($user) || $ank->vis_skype) {

        $post = $listing->post();
        $post->title = 'Skype';
        $post->content = $ank->skype;
        $post->icon = 'http://mystatus.skype.com/smallicon/' . $ank->skype;
        $post->url = 'skype:' . $ank->skype . '?chat';
    } else {

        $post = $listing->post();
        $post->title = 'Skype';
        $post->url = '/faq.php?info=hide&amp;return=' . URL;
        $post->content = __('Информация скрыта');
    }
}
//endregion

//region E-mail
if ($ank->email) {
    if ($ank->is_friend($user) || $ank->vis_email) {
        $post = $listing->post();
        $post->title = 'E-mail';
        $post->content = $ank->email;
        if (preg_match("#\@(mail|bk|inbox|list)\.ru$#i", $ank->email))
            $post->icon = 'http://status.mail.ru/?' . $ank->email;
        $post->url = 'mailto:' . $ank->email;

    } else {
        $post = $listing->post();
        $post->title = 'E-mail';
        $post->url = '/faq.php?info=hide&amp;return=' . URL;
        $post->content = __('Информация скрыта');
    }
}
//endregion

//region Регистрационный E-mail
if ($ank->reg_mail) {
    if ($user->group > $ank->group) {
        $post = $listing->post();
        $post->title = __('Регистрационный E-mail');
        $post->content = $ank->reg_mail;
        if (preg_match("#\@(mail|bk|inbox|list)\.ru$#i", $ank->reg_mail))
            $post->icon = 'http://status.mail.ru/?' . $ank->reg_mail;
        $post->url = 'mailto:' . $ank->reg_mail;
    }
}
//endregion

//region Webmoney
if ($ank->wmid) {
    $post = $listing->post();
    $post->title = 'WMID';
    $post->content = $ank->wmid;
    $post->url = 'http://passport.webmoney.ru/asp/certview.asp?wmid=' . $ank->wmid;
    $post->image = 'http://stats.wmtransfer.com/Levels/pWMIDLevel.aspx?wmid=' . $ank->wmid . '&amp;w=35&amp;h=16';
}
//endregion

//region Друзья
if ($ank->is_friend($user) || $ank->vis_friends) {
    $res = $db->prepare("SELECT COUNT(*) FROM `friends` WHERE `id_user` = ? AND `confirm` = '1'");
    $res->execute(Array($ank->id));
    $k_friends = $res->fetchColumn();

    $post = $listing->post();
    $post->title = __('Друзья');
    $post->url = $ank->id == $user->id ? "/my.friends.php" : "/profile.friends.php?id={$ank->id}";
    $post->counter = $k_friends;

} else {
    $post = $listing->post();
    $post->title = __('Друзья');
    $post->url = '/faq.php?info=hide&amp;return=' . URL;
    $post->content = __('Информация скрыта');
}
//endregion

//region Рейтинг
$post = $listing->post();
$post->title = __('Рейтинг');
$post->url = '/profile.reviews.php?id=' . $ank->id;
$post->counter = $ank->rating;
//endregion

//region Баллы
$post = $listing->post();
$post->title = __('Баллы');
$post->counter = $ank->balls;
//endregion

//region О себе
if ($ank->description) {
    $post = $listing->post();
    $post->title = __('О себе');
    $post->content[] = $ank->description;
}
//endregion

//region Список логинов
if ($ank->last_time_login){
    $q = $db->query("SELECT `login` FROM `login_history` WHERE `id_user` = '$ank->id' ORDER BY `time` DESC") ;
    $res = $q->fetchAll() ;
    $logins = array() ;
    foreach($res AS $v){
        $logins[] = $v['login'] ;
    }
    $post = $listing->post() ;
    $post->title = __('История логинов') ;
    $post->post = implode(', ', $logins) ;
    $post->url = '/profile.logins.php?id=' . $ank->id ;
}
//endregion

//region Последний визит
$post = $listing->post();
$post->title = __('Последний визит');
$post->content = misc::when($ank->last_visit);
//endregion

//region Всего переходов
$post = $listing->post();
$post->title = __('Всего переходов');
$post->content = $ank->conversions;
//endregion

//region Дата регистрации
$post = $listing->post();
$post->title = __('Дата регистрации');
$post->content = date('d-m-Y', $ank->reg_date);

//endregion

//region По приглашению от...

$q = $db->prepare("SELECT `id_user` FROM `invations` WHERE `id_invite` = ? LIMIT 1");
$q->execute(Array($ank->id));
if ($row = $q->fetch()) {
    $inv = new user($row['id_user']);
    $post = $listing->post();
    $post->title = text::toOutput(__('По приглашению от %s', '[user]' . $inv->id . '[/user]'));
}
//endregion
$listing->display();
//endregion

if ($user->group && $ank->id != $user->id) {
    $doc->act(__('Написать сообщение'), "my.mail.php?id={$ank->id}");
    if ($user->group > $ank->group) {
        $doc->act(__('Доступные действия'), "/dpanel/user.actions.php?id={$ank->id}");
    }
}

if ($user->group)
    $doc->ret(__('Личное меню'), '/menu.user.php');