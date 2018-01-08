<?php
include_once '../sys/inc/start.php';
use App\{document,pages,text,antiflood,listing,misc,listing_post,form,url,App};
use App\Models\{ChatMini,User};

$doc = new document;
$doc->template = 'chat_mini.template';

$doc->title = __('Мини чат');

$pages = new pages(ChatMini::count());

$can_write = true;
/** @var $user \user */
if (!$user->is_writeable) {
    $doc->msg(__('Писать запрещено'), 'write_denied');
    $can_write = false;
}

if ($can_write && $pages->this_page == 1) {
    if (isset($_POST['send']) && isset($_POST['message']) && isset($_POST['token']) && $user->group) {
        $message = (string)$_POST['message'];
        $users_in_message = text::nickSearch($message);
        $message = text::input_text($message);

        if (!antiflood::useToken($_POST['token'], 'chat_mini')) {
            // нет токена (обычно, повторная отправка формы)
        } elseif ($dcms->censure && $mat = is_valid::mat($message)) {
            $doc->err(__('Обнаружен мат: %s', $mat));
        } elseif ($message) {
            $user->balls += $dcms->add_balls_chat ;

            $comment = new ChatMini(['message' => $message, 'time' => TIME]);
            App::User(true)->chatMini()->save($comment);

            header('Refresh: 1; url=?' . passgen() . '&' . SID);
            $doc->ret(__('Вернуться'), '?' . passgen());
            $doc->msg(__('Сообщение успешно отправлено'));
            exit;
        } else {
            $doc->err(__('Сообщение пусто'));
        }

    }

    if ($user->group) {
        $message_form = '';
        if (isset($_GET ['message']) && is_numeric($_GET ['message'])) {
            $id_message = (int)$_GET ['message'];
            if ($message = ChatMini::find($id_message)) {
                $ank = User::find($message->id_user);
                echo $ank->icon . ' -';
                if (isset($_GET['reply'])) {
                    $message_form = '@' . $ank->login . ',';
                } elseif (isset($_GET['quote'])) {
                    $message_form = "[quote id_user=\"{$ank->id}\" time=\"{$message['time']}\"]{$message['message']}[/quote]";
                }
            }
        }

        $form = new form('?' . passgen());
        $form->hidden('token', antiflood::getToken('chat_mini'));
        $form->textarea('message', __('Сообщение'), $message_form, true);
        $form->button(__('Отправить'), 'send', false);
        $form->display();
    }
}

$messages = ChatMini::orderBy('id', 'DESC')->get()->forPage($pages->this_page, $user->items_per_page);
view('chat_mini.messages', compact('messages'));

$pages->display('?'); // вывод страниц

if ($user->group >= 3)
    $doc->act(__('Удаление сообщений'), 'message.delete_all.php');