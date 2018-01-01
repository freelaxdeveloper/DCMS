<?php

include_once '../sys/inc/start.php';
$doc = new document(1);
$doc->title = __('Журнал авторизаций');

$res = $db->prepare("SELECT COUNT(*) FROM `log_of_user_aut` WHERE `id_user` = ?");
$res->execute(Array($user->id));

$pages = new pages;
$pages->posts = $res->fetchColumn();
$q = $db->prepare("SELECT
        `log_of_user_aut`.`time` AS `time`,
        `log_of_user_aut`.`count` AS `count`,
        `log_of_user_aut`.`method` AS `method`,
        `log_of_user_aut`.`status` AS `status`,
        `log_of_user_aut`.`iplong` AS `iplong`,
        `browsers`.`name` AS `browser`
        FROM `log_of_user_aut`
LEFT JOIN `browsers` ON `browsers`.`id` = `log_of_user_aut`.`id_browser`
WHERE `log_of_user_aut`.`id_user` = ?
ORDER BY `time` DESC
LIMIT " . $pages->limit . ";");
$q->execute(Array($user->id));

$listing = new listing();
while ($log = $q->fetch()) {
    $post = $listing->post();
    $post->counter = $log['count']; /* кол-во входов с этими IP+UA+домен+метод+статус */
    $post->title = $log['method'] . ': ' . __($log['status'] ? 'Удачно' : 'Не удачно');
    $post->highlight = !$log['status'];
    $post->content = text::toOutput($log['browser'] . "\n" . long2ip($log['iplong']));
    $post->time = misc::when($log['time']);
}
$listing->display(__('Журнал пуст'));

$pages->display('?'); // вывод страниц

$doc->ret(__('Личное меню'), '/menu.user.php');