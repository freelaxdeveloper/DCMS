<?php
include_once '../sys/inc/start.php';
use App\{dpanel,document,text,listing,misc,form};

dpanel::check_access();
$doc = new document(5);
$doc->title = __('Изменение рекламы');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Refresh: 1; url=adt.php');
    $doc->ret(__('Реклама и баннеры'), 'adt.php');
    $doc->ret(__('Админка'), '/dpanel/');
    $doc->err(__('Ошибка выбора рекламы'));
    exit;
}
$id_adt = (int) $_GET['id'];

$q = $db->prepare("SELECT * FROM `advertising` WHERE `id` = ?");
$q->execute(Array($id_adt));

if (!$adt = $q->fetch()) {
    header('Refresh: 1; url=adt.php?id=' . $id_adt);
    $doc->ret(__('Вернуться'), 'adt.php?id=' . $id_adt);
    $doc->ret(__('Реклама и баннеры'), 'adt.php');
    $doc->ret(__('Админка'), '/dpanel/');
    $doc->err(__('Рекламная позиция не найдена'));
    exit;
}


if (isset($_POST['delete'])) {
    if (empty($_POST['captcha']) || empty($_POST['captcha_session']) || !captcha::check($_POST['captcha'], $_POST['captcha_session'])) {
        $doc->err(__('Проверочное число введено неверно'));
    } else {
        $res = $db->prepare("DELETE FROM `advertising` WHERE `id` = ?");
        $res->execute(Array($adt['id']));

        header('Refresh: 1; url=adt.php?id=' . $adt['space']);
        $doc->msg(__('Рекламная площадка успешно удалена'));

        $dcms->log('Реклама', 'Удаление рекламной площадки ' . $adt['name'] . ' (' . $adt['url_link'] . ')');

        $doc->ret(__('Вернуться'), "adt.php?id=$adt[space]");
        $doc->ret(__('Рекламные позиции'), 'adt.php');
        $doc->ret(__('Админка'), '/dpanel/');
        exit;
    }
}

if (isset($_POST['common'])) {
    if (isset($_POST['name'])) {
        $name = text::input_text($_POST['name']);
        if ($name && $name != $adt['name']) {
            $dcms->log('Реклама', 'Изменение названия рекламной площадки ' . $adt['name'] . ' на [url="/dpanel/adt.edit.php?id=' . $id_adt . '"]' . $name . '[/url]');

            $adt['name'] = $name;
            $res = $db->prepare("UPDATE `advertising` SET `name` = ? WHERE `id` = ? LIMIT 1");
            $res->execute(Array($adt['name'], $id_adt));
            $doc->msg(__('Название успешно изменено'));
        } elseif (!$name)
            $doc->err(__('Название не может быть пустым'));
    }

    $bold = (int) !empty($_POST['bold']);

    if ($adt['bold'] != $bold) {
        $adt['bold'] = $bold;

        $res = $db->prepare("UPDATE `advertising` SET `bold` = ? WHERE `id` = ? LIMIT 1");
        $res->execute(Array($bold, $id_adt));
        if ($adt['bold']) {
            $dcms->log('Реклама', 'Изменение рекламной площадки [url="/dpanel/adt.edit.php?id=' . $id_adt . '"]' . $name . '[/url] (установка жирности)');
            $doc->msg(__('Реклама будет выделяться жирным шрифтом'));
        } else {
            $dcms->log('Реклама', 'Изменение рекламной площадки [url="/dpanel/adt.edit.php?id=' . $id_adt . '"]' . $name . '[/url] (снятие жирности)');
            $doc->msg(__('Реклама не будет выделяться жирным шрифтом'));
        }
    }

    if (isset($_POST['url_link'])) {
        $url_link = text::input_text($_POST['url_link']);
        if ($url_link && $url_link != $adt['url_link']) {
            $adt['url_link'] = $url_link;
            $res = $db->prepare("UPDATE `advertising` SET `url_link` = ? WHERE `id` = ? LIMIT 1");
            $res->execute(Array($adt['url_link'], $id_adt));
            $dcms->log('Реклама', 'Изменение рекламной площадки [url="/dpanel/adt.edit.php?id=' . $id_adt . '"]' . $name . '[/url] (ссылка: ' . $adt['url_link'] . ')');
            $doc->msg(__('Адрес ссылки успешно изменен'));
        } elseif (!$url_link)
            $doc->err(__('Адрес ссылки не может быть пуст'));
    }

    if (isset($_POST['url_img'])) {
        $url_img = text::input_text($_POST['url_img']);
        if ($url_img != $adt['url_img']) {
            $adt['url_img'] = $url_img;
            $res = $db->prepare("UPDATE `advertising` SET `url_img` = ? WHERE `id` = ? LIMIT 1");
            $res->execute(Array($adt['url_img'], $id_adt));
            $dcms->log('Реклама', 'Изменение рекламной площадки [url="/dpanel/adt.edit.php?id=' . $id_adt . '"]' . $name . '[/url] (адрес изображения: ' . $adt['url_img'] . ')');
            $doc->msg(__('Адрес изображения успешно изменен'));
        }
    }

    $page_main = (int) (isset($_POST['page_main']) && $_POST['page_main']);
    $page_other = (int) (isset($_POST['page_other']) && $_POST['page_other']);

    if (!$page_main && !$page_other)
        $doc->err(__('Реклама должна же где-то отображаться'));
    elseif ($page_main != $adt['page_main'] || $page_other != $adt['page_other']) {
        $adt['page_main'] = $page_main;
        $adt['page_other'] = $page_other;
        $res = $db->prepare("UPDATE `advertising` SET `page_main` = ?, `page_other` = ? WHERE `id` = ? LIMIT 1");
        $res->execute(Array($adt['page_main'], $adt['page_other'], $id_adt));
        $dcms->log('Реклама', 'Изменение рекламной площадки [url="/dpanel/adt.edit.php?id=' . $id_adt . '"]' . $name . '[/url] (место отображения)');
        $doc->msg(__('Место отображения рекламы изменено'));
    }
}

if (isset($_POST['time'])) {
    $always = (int) (isset($_POST['always']) && $_POST['always']);
    if ($adt['time_end']) {
        if ($always) {
            $adt['time_end'] = 0;
            $res = $db->prepare("UPDATE `advertising` SET `time_end` = '0' WHERE `id` = ? LIMIT 1");
            $res->execute(Array($id_adt));
            $dcms->log('Реклама', 'Изменение рекламной площадки [url="/dpanel/adt.edit.php?id=' . $id_adt . '"]' . $adt['name'] . '[/url] (вечный показ)');
            $doc->msg(__('Вечный показ включен'));
        } else {
            if (isset($_POST['add']) && isset($_POST['mn'])) {
                $add = (int) $_POST['add'];
                $mn = (int) $_POST['mn'];
                // сбрасываем счетчики, если реклама была не активна
                if ($adt['time_start'] && $adt['time_start'] > TIME || $adt['time_end'] && $adt['time_end'] < TIME) {
                    $doc->msg(__('Счетчики показов и переходов сброшены'));
                    $clear_counters_sql = "`count_show_light` =  '0', `count_out_light` =  '0', `count_show_full` =  '0', `count_out_full` =  '0', ";
                }else
                    $clear_counters_sql = '';

                if ($add && $mn) {
                    if ($adt['time_end'] > TIME)
                        $adt['time_end'] = $adt['time_end'] + $add * $mn * 60 * 60 * 24;
                    else {
                        $adt['time_start'] = TIME;
                        $adt['time_end'] = TIME + $add * $mn * 60 * 60 * 24;
                    }

                    $res = $db->prepare("UPDATE `advertising` SET $clear_counters_sql`time_end` = ?, `time_start` = ? WHERE `id` = ? LIMIT 1");
                    $res->execute(Array($adt['time_end'], $adt['time_start'], $id_adt));
                    $doc->msg(__('Время завершения показа обновлено'));
                }else
                    $doc->err(__('Не корректное время показа'));
            }
        }
    }else {
        if (!$always) {
            $adt['time_end'] = TIME;
            $dcms->log('Реклама', 'Изменение рекламной площадки [url="/dpanel/adt.edit.php?id=' . $id_adt . '"]' . $adt['name'] . '[/url] (вечный показ отключен)');
            $res = $db->prepare("UPDATE `advertising` SET `time_end` = ? WHERE `id` = ? LIMIT 1");
            $res->execute(Array(TIME, $id_adt));
            $doc->msg(__('Вечный показ отключен'));
        }
    }
}

$listing = new listing();

$post = $listing->post();
$post->icon('adt');
$post->title = text::toValue($adt['name']);
$post->highlight = true;


if ($adt['time_create']) {
    $post = $listing->post();
    $post->title = __('Дата создания');
    $post->content = misc::when($adt['time_create']);
}

$post = $listing->post();
$post->title = __('Начало показа');
if (!$adt['time_start']) {
    $post->content = __('Нет данных');
} elseif ($adt['time_start'] > TIME) {
    $post->highlight = true;
    $post->content = misc::when($adt['time_start']);
} else {
    $post->content = misc::when($adt['time_start']);
}

$post = $listing->post();
$post->title = __('Конец показа');
if (!$adt['time_end'])
    $post->content = __('Бесконечный показ');
else
    $post->content = misc::when($adt['time_end']);

$listing->display();

if (!isset($_GET['delete'])) {

    $form = new form("?id=$id_adt&amp;" . passgen());
    $form->text('name', __('Название'), $adt['name']);
    $form->checkbox('bold', __('Выделить жирным'), $adt['bold']);
    $form->text('url_link', __('Адрес ссылки'), $adt['url_link']);
    $form->text('url_img', __('Адрес изображения'), $adt['url_img']);
    $form->checkbox('page_main', __('На главной'), $adt['page_main']);
    $form->checkbox('page_other', __('На остальных'), $adt['page_other']);
    $form->button(__('Применить'), 'common');
    $form->display();

    $form = new form("?id=$id_adt&amp;" . passgen());
    if ($adt['time_end']) {
        $form->text('add', __('Добавить к времени отображения'), 1, false, 3);
        $options = array();
        $options[] = array('1', __('Дней'));
        $options[] = array('7', __('Недель'), 1);
        $options[] = array('31', __('Месяцев'));
        $form->select('mn', false, $options);
    }
    $form->checkbox('always', __('Отображать бесконечно'), !$adt['time_end']);
    if ($adt['time_start'] && $adt['time_start'] >= TIME || $adt['time_end'] && $adt['time_end'] <= TIME)
        $form->bbcode('[notice] ' . __('Счетчики показов и переходов будут сброшены'));
    $form->button(__('Применить'), 'time');
    $form->display();
}else {
    $form = new form("?id=$id_adt&amp;delete&amp;" . passgen());
    $form->captcha();
    $form->bbcode(__('Подтвердите удаление рекламной позиции'));
    $form->button(__('Удалить'), 'delete');
    $form->display();
}

$doc->ret(__('Вернуться'), "adt.php?id=$adt[space]");
$doc->ret(__('Рекламные площадки'), 'adt.php');
$doc->ret(__('Админка'), '/dpanel/');
