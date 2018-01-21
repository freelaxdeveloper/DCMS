<?php
include_once '../sys/inc/start.php';
use App\{document,captcha,form,url,misc};
use App\App\App;

$doc = new document();
$doc->title = __('Очистка категории');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Refresh: 1; url=./');
    $doc->err(__('Ошибка выбора категории'));
    exit;
}
$id_category = (int) $_GET['id'];

$q = $db->prepare("SELECT * FROM `forum_categories` WHERE `id` = ? AND `group_edit` <= ?");
$q->execute(Array($id_category, App::user()->group));

if (!$category = $q->fetch()) {
    header('Refresh: 1; url=./');
    $doc->err(__('Категория не доступна для чистки'));
    exit;
}


$doc->title = __('Чистка категории "%s"', $category['name']); // шапка страницы

if (isset($_POST['clear'])) {
    if (empty($_POST['captcha']) || empty($_POST['captcha_session']) || !captcha::check($_POST['captcha'],
            $_POST['captcha_session'])) {
        $doc->err(__('Проверочное число введено неверно'));
    } else {
        $doc->err('На данный момент не реализовано');
    }
}

$res = $db->prepare("SELECT COUNT(*) FROM `forum_themes` WHERE `id_category` = ?");
$res->execute(Array($category['id']));

$form = new form(new url());
$count['themes_all'] = $res->fetchColumn();
$form->bbcode(__('Всего тем в категории: %s', '[b]' . $count['themes_all'] . '[/b]'));

// темы, в которых была активность более года назад
$res = $db->prepare("SELECT COUNT(*) FROM `forum_themes` WHERE `id_category` = ? AND `top` = '0' AND `time_last` < ?");
$res->execute(Array($category['id'], (TIME - 31536000)));
$count['themes_old'] = $res->fetchColumn();
// echo "Не активные более года: $count[themes_old]<br />";
// темы, закрытые более трех месяцев
$res = $db->prepare("SELECT COUNT(*) FROM `forum_themes` WHERE `id_category` = ? AND `top` = '0' AND `group_write` > '1' AND `time_last` < ?");
$res->execute(Array($category['id'], (TIME - 7884000)));
$count['themes_old2'] = $res->fetchColumn();
// echo "Закрытые более трех месяцев: $count[themes_old2]<br />";
if (!$count['themes_old'] + $count['themes_old2']) $form->bbcode(__('[b]' . 'Категория не требует очистки' . '[/b]'));

$form->checkbox('themes_old',
    __('Не активные более года: %d ' . misc::number($count['themes_old'], 'тема', 'темы', 'тем'), $count['themes_old']),
    (bool) $count['themes_old']);
$form->checkbox('themes_old2',
    __('Закрыто более 3-х месяцев: %d ' . misc::number($count['themes_old2'], 'тема', 'темы', 'тем'),
        $count['themes_old2']), (bool) $count['themes_old2']);
$form->captcha();
$form->bbcode('* ' . __('Данные будут безвозвратно удалены'));
$form->button(__('Чистить'), 'clear');
$form->display();

$doc->act(__('Параметры категории'), 'category.edit.php?id=' . $category['id']);
$doc->ret(__('В категорию'), 'category.php?id=' . $category['id']);
$doc->ret(__('Форум'), './');
