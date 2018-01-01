<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$advertisement = new adt();
$doc = new document(5);
$doc->title = __('Новая рекламная площадка');

if (!isset($_GET['id'])) {
    header('Refresh: 1; url=adt.php');
    $doc->ret(__('Реклама и баннеры'), 'adt.php');
    $doc->ret(__('Админка'), '/dpanel/');
    $doc->err(__('Ошибка выбора позиции'));
    exit;
}
$id_space = (string) $_GET['id'];

if (!$name = $advertisement->getNameById($id_space)) {
    header('Refresh: 1; url=?');
    $doc->err(__('Выбраная позиция отсутствует'));
    exit;
}

if (isset($_POST['create'])) {
    $name_adt = text::input_text(@$_POST['name']);
    $url = text::input_text(@$_POST['url_link']);
    $url_img = text::input_text(@$_POST['url_img']);
    $bold = (int) (isset($_POST['bold']) && $_POST['bold']);
    $on_main = (int) (isset($_POST['page_main']) && $_POST['page_main']);
    $on_other = (int) (isset($_POST['page_other']) && $_POST['page_other']);

    if (isset($_POST['always']) && $_POST['always']) {
        // будет вечный показ рекламы
        $time1 = 0;
        $time2 = 0;
    } else {
        $starttime = (int) @$_POST['starttime'];
        $start = (int) @$_POST['start'];
        $lifetime = (int) @$_POST['lifetime'];
        $life = (int) @$_POST['life'];
        // начало показа
        $time1 = max(TIME + $starttime * $start * 60, TIME);
        // конец показа
        $time2 = $time1 + $lifetime * $life * 60 * 60 * 24;
    }

    if (empty($_POST['captcha']) || empty($_POST['captcha_session']) || !captcha::check($_POST['captcha'], $_POST['captcha_session'])) {
        $doc->err(__('Проверочное число введено неверно'));
    } elseif ($name_adt && $url && $url != 'http://') {
        $res = $db->prepare("INSERT INTO `advertising` (`space`, `url_link`, `name`, `url_img`, `page_main`, `page_other`, `time_create`, `time_start`, `time_end`, `bold`)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $res->execute(Array($id_space, $url, $name_adt, $url_img, $on_main, $on_other, TIME, $time1, $time2, $bold));
        header('Refresh: 1; url=adt.php?id=' . $id_space);

        $dcms->log('Реклама', 'Добавление рекламной площадки [url=/dpanel/adt.php?id=' . $id_space . ']' . $name_adt . '[/url]');

        $doc->msg(__('Рекламная площадка успешно добавлена'));
        $doc->ret(__('Вернуться'), "adt.php?id=$id_space");
        $doc->ret(__('Рекламные позиции'), 'adt.php');
        $doc->ret(__('Админка'), '/dpanel/');
        exit;
    }else
        $doc->err(__('Необходимые поля не заполнены'));
}

$form = new form("?id=$id_space&amp;" . passgen());

$form->text('name', __('Название'));
$form->checkbox('bold', __('Выделить жирным'));
$form->text('url_link', __('Адрес ссылки'), 'http://');
$form->text('url_img', __('Адрес изображения'), 'http://');

$form->checkbox('page_main', __('На главной'), true);
$form->checkbox('page_other', __('На остальных'), true);

$form->text('lifetime', __('Время действия'), 1, false, 3);
$options = array();
$options[] = array('1', __('Дней'));
$options[] = array('7', __('Недель'), 1);
$options[] = array('31', __('Месяцев'));
$form->select('life', false, $options);

$form->text('starttime', __('Начало показа через'), 1, false, 3);
$options = array();
$options[] = array(0, __('Немедленно'));
$options[] = array(1, __('Минут'));
$options[] = array(60, __('Часов'));
$options[] = array(60 * 24, __('Суток'));
$options[] = array(60 * 24 * 7, __('Недель'));
$form->select('start', false, $options);

$form->checkbox('always', __('Отображать бесконечно'));
$form->captcha();
$form->button(__('Создать'), 'create');
$form->display();


$doc->ret(__('Вернуться'), "adt.php?id=$id_space");
$doc->ret(__('Рекламные позиции'), 'adt.php');
$doc->ret(__('Админка'), '/dpanel/');
