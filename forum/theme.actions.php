<?php

include_once '../sys/inc/start.php';
use App\{document,listing};
use App\App\App;

$doc = new document(1);
$doc->title = __('Форум');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Refresh: 1; url=./');
    $doc->err(__('Ошибка выбора темы'));
    exit;
}
$id_theme = (int) $_GET['id'];
$q = $db->prepare("SELECT `forum_themes`.* ,
        `forum_categories`.`name` AS `category_name` ,
        `forum_topics`.`name` AS `topic_name`,
        `forum_topics`.`group_write` AS `topic_group_write`
FROM `forum_themes`
LEFT JOIN `forum_categories` ON `forum_categories`.`id` = `forum_themes`.`id_category`
LEFT JOIN `forum_topics` ON `forum_topics`.`id` = `forum_themes`.`id_topic`
WHERE `forum_themes`.`id` = ? AND `forum_themes`.`group_show` <= ? AND `forum_topics`.`group_show` <= ? AND `forum_categories`.`group_show` <= ?");
$q->execute(Array($id_theme, App::user()->group, App::user()->group, App::user()->group));
if (!$theme = $q->fetch()) {
    header('Refresh: 1; url=./');
    $doc->err(__('Тема не доступна'));
    exit;
}

$doc->title = __('Тема %s - действия', $theme['name']);

$listing = new listing();

if ($theme['group_edit'] <= App::user()->group) {
    $post = $listing->post();
    $post->url = 'theme.status.php?id=' . $theme['id'];
    $post->title = $theme['group_write'] > $theme['topic_group_write'] ? __('Открыть тему') : __('Закрыть тему');
    $post->icon($theme['group_write'] > $theme['topic_group_write'] ? 'lock' : 'unlock');
}
if ($theme['group_edit'] <= App::user()->group) {
    $post = $listing->post();
    $post->url = 'theme.rename.php?id=' . $theme['id'];
    $post->title = __('Переименовать');
    $post->icon('rename');
}
if ($theme['group_edit'] <= App::user()->group) {
    $post = $listing->post();
    $post->url = 'theme.move.php?id=' . $theme['id'];
    $post->title = __('Переместить');
    $post->icon('move');
}
if ($theme['group_edit'] <= App::user()->group) {
    $post = $listing->post();
    $post->url = 'theme.security.php?id=' . $theme['id'];
    $post->title = __('Разрешения');
    $post->icon('security');
}
if ($theme['group_edit'] <= App::user()->group && App::user()->group >= 5) {
    $post = $listing->post();
    $post->url = 'theme.moderator.php?id=' . $theme['id'];
    $post->title = __('Назначить модератора');
    $post->icon('admin.1');
}
if (!$theme['id_vote'] && $theme['group_write'] <= App::user()->group && App::user()->group >= 2) {
    $post = $listing->post();
    $post->url = 'vote.new.php?id_theme=' . $theme['id'];
    $post->title = __('Создать голосование');
    $post->icon('create');
}
if ($theme['id_vote'] && $theme['group_write'] <= App::user()->group && App::user()->group >= 2) {
    $post = $listing->post();
    $post->url = 'vote.edit.php?id_theme=' . $theme['id'];
    $post->title = __('Изменить голосование');
    $post->icon('vote');
}
if ($theme['group_edit'] <= App::user()->group && App::user()->group >= 2 || App::user()->id == $theme['id_moderator']) {
    $post = $listing->post();
    $post->url = 'theme.posts.delete.php?id=' . $theme['id'];
    $post->title = __('Удаление сообщений');
    $post->icon('delete');
}
if ($theme['group_edit'] <= App::user()->group && App::user()->group >= 2) {
    $post = $listing->post();
    $post->url = 'theme.delete.php?id=' . $theme['id'];
    $post->title = __('Удаление темы');
    $post->icon('delete');
}

$listing->display();

$doc->ret(__('Вернуться в тему'), 'theme.php?id=' . $theme['id']);
$doc->ret($theme['topic_name'], 'topic.php?id=' . $theme['id_topic']);
$doc->ret($theme['category_name'], 'category.php?id=' . $theme['id_category']);
$doc->ret(__('Форум'), './');
?>