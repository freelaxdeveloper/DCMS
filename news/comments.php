<?php
include_once '../sys/inc/start.php';
use App\{document,text,listing,user,misc,pages,antiflood,is_valid,form,url};
use App\Models\{News,NewsComment};
use App\App\App;

$doc = new document();
$doc->title = __('Комментарии к новости');
$doc->ret(__('Все новости'), './');

$id_news = (int) $_GET['id'];

if (!$news = News::find($id_news)) $doc->access_denied(__('Новость не найдена или удалена'));

$can_write = true;
if (!$user->is_writeable) {
    $doc->msg(__('Писать запрещено'), 'write_denied');
    $can_write = false;
}

$pages = new pages(NewsComment::where('id_news', $news->id)->count());

if ($can_write) {

    if (isset($_POST['send']) && isset($_POST['comment']) && isset($_POST['token']) && $user->group) {

        $text = (string) $_POST['comment'];
        $users_in_message = text::nickSearch($text);
        $text = text::input_text($text);

        if (!antiflood::useToken($_POST['token'], 'news')) {
            // нет токена (обычно, повторная отправка формы)
        } elseif ($dcms->censure && $mat = is_valid::mat($text)) $doc->err(__('Обнаружен мат: %s', $mat));
        elseif ($text) {
            App::user()->increment('balls');
            $comment = NewsComment::create([
                'id_news' => $news->id,
                'id_user' => App::user()->id,
                'time' => TIME,
                'text' => $text,
            ]);
            header('Refresh: 1; url=?id=' . $news->id . '&' . passgen());
            $doc->ret(__('Вернуться'), '?id=' . $news->id . '&amp;' . passgen());
            $doc->msg(__('Комментарий успешно отправлен'));

            if ($users_in_message) {
                for ($i = 0; $i < count($users_in_message) && $i < 20; $i++) {
                    $user_id_in_message = $users_in_message[$i];
                    if ($user_id_in_message == $user->id) {
                        continue;
                    }
                    $ank_in_message = new user($user_id_in_message);
                    if ($ank_in_message->notice_mention) {
                        $ank_in_message->mess("[user]{$user->id}[/user] упомянул" . ($user->sex ? '' : 'а') . " о Вас в [url=/news/comments.php?id={$news['id']}#comment{$comment->id}]комментарии[/url] к новости");
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
        $form = $form->fetch();
    }
}

$comments = NewsComment::where('id_news', $news->id)->orderBy('id', 'desc')
    ->get()->forPage($pages->this_page, App::user()->items_per_page);

view('news.comments', compact('comments', 'news', 'form'));

$pages->display("?id={$news->id}&amp;"); // вывод страниц