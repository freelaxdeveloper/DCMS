<?php
include_once '../sys/inc/start.php';
use App\{document,pages,listing,misc,text};
use App\Models\User;

$doc = new document(2);
$doc->title = __('Лог действий');

if (isset($_GET['id_user'])) {
    $id_user = 'all';
    $sql_where = ' WHERE 1 = 1';

    if ($_GET['id_user'] !== 'all') {
        $ank = User::find($_GET['id_user']);
        $doc->title .= ' "' . $ank->login . '"';
        $id_user = $ank->id;
        $sql_where = " WHERE `id_user` = '$ank->id'";
    }

    if (!empty($_GET['module'])) {
        $module = (string)$_GET['module'];
        // вывод списка действий по модулю
        $res = $db->prepare("SELECT COUNT(*) FROM `action_list_administrators` " . $sql_where . " AND `module` = ?");
        $res->execute(Array($module));
        $listing = new listing();
        $pages = new pages;
        $pages->posts = $res->fetchColumn(); // количество
        $q = $db->prepare("SELECT * FROM `action_list_administrators` " . $sql_where . " AND `module` = ? ORDER BY `id` DESC LIMIT $pages->limit");
        $q->execute(Array($module));
        if ($arr = $q->fetchAll()) {
            foreach ($arr AS $action) {
                $ank = User::find($action['id_user']);
                $post = $listing->post();
                $post->title = $ank->login;
                $post->time = misc::when($action['time']);
                $post->content = text::toOutput($action['description']);
            }
        }
        $listing->display(__('Действия отсутствуют'));

        $pages->display('?id_user=' . $id_user . '&amp;module=' . urlencode($module) . '&amp;'); // вывод страниц
        $doc->ret(__('К модулям'), '?id_user=' . $id_user . '&amp;' . passgen());
        $doc->ret(__('Администраторы'), '?' . passgen());

        exit;
    }
    // вывод списка модулей

    $listing = new listing();

    $pages = new pages;
    $res = $db->query("SELECT COUNT(DISTINCT(`module`)) FROM `action_list_administrators`$sql_where");
    $pages->posts = $res->fetchColumn(); // количество модулей
    $q = $db->query("SELECT `module` FROM `action_list_administrators`$sql_where GROUP BY `module` LIMIT " . $pages->limit);
    while ($module = $q->fetch()) {
        $post = $listing->post();
        $post->title = __($module['module']);
        $post->url = '?id_user=' . $id_user . '&amp;module=' . urlencode($module['module']);
    }

    $listing->display(__('Модули отсутствуют'));

    $pages->display('?id_user=' . $id_user); // вывод страниц
    $doc->ret(__('Администраторы'), '?' . passgen());
    exit;
}
// вывод списка администраторов

$listing = new listing();
$month_time = mktime(0, 0, 0, date('n'), 0); // начало текущего месяца
$q = $db->prepare("SELECT *, COUNT(`id`) AS `count` FROM `action_list_administrators` WHERE `time` > ? GROUP BY `id_user` ORDER BY `count` DESC");
$q->execute(Array($month_time));
$post = $listing->post();
$post->title = __('Все администраторы');
$post->url = '?id_user=all';

if ($arr = $q->fetchAll()) {
    foreach ($arr AS $ank_q) {
        $post = $listing->post();
        $ank = User::find($ank_q['id_user']);
        $post->title = $ank->login;
        $post->counter = $ank_q['count'];
        $post->url = '?id_user=' . $ank->id;
        $post->icon($ank->icon());
    }
}
$listing->display(__('Нет администрации'));

$doc->ret(__('Админка'), './');