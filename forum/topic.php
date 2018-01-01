<?php

include_once '../sys/inc/start.php';
include 'inc/functions.php';
$doc = new document();

$doc->title = __('Форум');
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Refresh: 1; url=./');
    $doc->err(__('Ошибка выбора раздела'));

    exit;
}
$id_top = (int)$_GET['id'];

$q = $db->prepare("SELECT `forum_topics`.*, `forum_categories`.`name` AS `category_name` FROM `forum_topics`
JOIN `forum_categories` ON `forum_categories`.`id` = `forum_topics`.`id_category`
WHERE `forum_topics`.`id` = ? AND `forum_topics`.`group_show` <= ? AND `forum_categories`.`group_show` <= ?");
$q->execute(Array($id_top, $user->group, $user->group));
if (!$topic = $q->fetch()) {
    header('Refresh: 1; url=./');
    $doc->err(__('Раздел не доступен'));
    exit;
}

$doc->title .= ' - ' . $topic['name'];

$res = $db->prepare("SELECT COUNT(*) FROM `forum_themes` WHERE `id_topic` = ? AND `group_show` <= ?");
$res->execute(Array($topic['id'], $user->group));
$posts = array();
$pages = new pages;
$pages->posts = $res->fetchColumn();

$q = $db->prepare("SELECT `forum_themes`.*
        FROM `forum_themes`
JOIN `forum_messages` ON `forum_messages`.`id_theme` = `forum_themes`.`id`
 WHERE `forum_themes`.`id_topic` = ? AND `forum_themes`.`group_show` <= ? AND `forum_messages`.`group_show` <= ?
 GROUP BY `forum_themes`.`id`
 ORDER BY `forum_themes`.`top`, `forum_themes`.`time_last` DESC LIMIT " . $pages->limit);
$q->execute(Array($topic['id'], $user->group, $user->group));
$listing = new listing();

if ($arr = $q->fetchAll()) {

    $themes_ids = array();
    foreach ($arr AS $theme) {
        $themes_ids[] = $theme['id'];
    }
    $themes_msg_counters = forum_getMessagesCounters($themes_ids, 0, current_user::getInstance()->group);
    $themes_views_counters = forum_getViewsCounters($themes_ids);

    foreach ($arr AS $theme) {
        $post = $listing->post();

        $is_open = (int)($theme['group_write'] <= $topic['group_write']);

        $post->icon("forum.theme.{$theme['top']}.$is_open");
        $post->title = text::toValue($theme['name']);
        $post->url = 'theme.php?id=' . $theme['id'];
        $post->counter = $themes_msg_counters[$theme['id']];
        $post->time = misc::when($theme['time_last']);


        $autor = new user($theme['id_autor']);
        $last_msg = new user($theme['id_last']);

        $post->content = ($autor->id != $last_msg->id ? $autor->nick . '/' . $last_msg->nick : $autor->nick) . '<br />';
        $post->bottom = __('Просмотров: %s', $themes_views_counters[$theme['id']]);
    }
}


$listing->display(__('Доступных Вам тем нет'));

$pages->display('topic.php?id=' . $topic['id'] . '&amp;'); // вывод страниц

if ($topic['group_write'] <= $user->group) {
    $doc->act(__('Начать новую тему'), 'theme.new.php?id_topic=' . $topic['id'] . "&amp;return=" . URL);
}

if ($topic['group_edit'] <= $user->group) {
    $doc->act(__('Параметры раздела'), 'topic.edit.php?id=' . $topic['id'] . "&amp;return=" . URL);
}

$doc->ret($topic['category_name'], 'category.php?id=' . $topic['id_category']);
$doc->ret(__('Форум'), './');