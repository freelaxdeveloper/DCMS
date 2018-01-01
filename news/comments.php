<?php
include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = __('Комментарии к новости');
$doc->ret(__('Все новости'), './');

$id = (int) @$_GET['id'];

$q = $db->prepare("SELECT * FROM `news` WHERE `id` = ? LIMIT 1");
$q->execute(Array($id));

if (!$news = $q->fetch()) $doc->access_denied(__('Новость не найдена или удалена'));



$listing = new listing();
$post = $listing->post();
$ank = new user((int) $news['id_user']);


$post->icon('news');
$post->content = text::toOutput($news['text']);
$post->title = text::toValue($news['title']);
$post->time = misc::when($news['time']);
$post->bottom = '<a href="/profile.view.php?id=' . $news['id_user'] . '">' . $ank->nick() . '</a>';

if ($user->group >= max($ank->group, 4)) {
    if (!$news['sended']) {
        $post->action('send', "news.send.php?id=$news[id]");
    }
    $post->action('edit', "news.edit.php?id=$news[id]"); // редактирование
    $post->action('delete', "news.delete.php?id=$news[id]"); // удаление
}
$listing->display();

$ank = new user($news['id_user']);

$can_write = true;
if (!$user->is_writeable) {
    $doc->msg(__('Писать запрещено'), 'write_denied');
    $can_write = false;
}

$res = $db->prepare("SELECT COUNT(*) FROM `news_comments` WHERE `id_news` = ?");
$res->execute(Array($news['id']));
$pages = new pages;
$pages->posts = $res->fetchColumn(); // количество сообщений

if ($can_write) {

    if (isset($_POST['send']) && isset($_POST['comment']) && isset($_POST['token']) && $user->group) {

        $text = (string) $_POST['comment'];
        $users_in_message = text::nickSearch($text);
        $text = text::input_text($text);

        if (!antiflood::useToken($_POST['token'], 'news')) {
            // нет токена (обычно, повторная отправка формы)
        } elseif ($dcms->censure && $mat = is_valid::mat($text)) $doc->err(__('Обнаружен мат: %s', $mat));
        elseif ($text) {
            $user->balls++;
            $res = $db->prepare("INSERT INTO `news_comments` (`id_news`, `id_user`, `time`, `text`) VALUES (?,?,?,?)");
            $res->execute(Array($news['id'], $user->id, TIME, $text));
            header('Refresh: 1; url=?id=' . $id . '&' . passgen());
            $doc->ret(__('Вернуться'), '?id=' . $id . '&amp;' . passgen());
            $doc->msg(__('Комментарий успешно отправлен'));

            $id_message = $db->lastInsertId();


            if ($users_in_message) {
                for ($i = 0; $i < count($users_in_message) && $i < 20; $i++) {
                    $user_id_in_message = $users_in_message[$i];
                    if ($user_id_in_message == $user->id) {
                        continue;
                    }
                    $ank_in_message = new user($user_id_in_message);
                    if ($ank_in_message->notice_mention) {
                        $ank_in_message->mess("[user]{$user->id}[/user] упомянул" . ($user->sex ? '' : 'а') . " о Вас в [url=/news/comments.php?id={$news['id']}#comment{$id_message}]комментарии[/url] к новости");
                    }
                }
            }


            exit;
        } else {
            $doc->err(__('Комментарий пуст'));
        }
    }

    if ($user->group) {
        $form = new form(new url());
        $form->hidden('token', antiflood::getToken('news'));
        $form->textarea('comment', __('Комментарий'));
        $form->button(__('Отправить'), 'send', false);
        $form->button(__('Обновить'), 'refresh');
        $form->display();
    }
}


$q = $db->prepare("SELECT * FROM `news_comments` WHERE `id_news` = ? ORDER BY `id` DESC LIMIT $pages->limit");
$q->execute(Array($news['id']));

$listing = new listing();
if ($arr = $q->fetchAll()) {
    foreach ($arr AS $message) {
        $post = $listing->post();
        $ank = new user($message['id_user']);
        $post->title = $ank->nick();
        $post->url = '/profile.view.php?id=' . $ank->id;
        $post->icon($ank->icon());
        $post->time = misc::when($message['time']);

        if ($user->group >= 2) {
            $post->action('delete', "comment.delete.php?id=$message[id]&amp;return=" . URL);
        }

        $post->content[] = $message['text'];
    }
}

$listing->display(__('Комментарии отсутствуют'));

$pages->display('?id=' . $id . '&amp;'); // вывод страниц