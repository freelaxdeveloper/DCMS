<?php
include_once '../sys/inc/start.php';
use App\{document,user,groups,form,url,text};
use App\Models\{ForumMessage,ForumHistory};
use App\App\App;

$doc = new document(1);
$doc->title = __('Редактирование сообщения');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $doc->toReturn();
    $doc->err(__('Ошибка выбора сообщения'));
    exit;
}

$id_message = (int) $_GET['id'];

if (!$message = ForumMessage::find($id_message)) {
    $doc->toReturn();
    $doc->err(__('Сообщение не найдено'));

    exit;
}

$access_edit = false;
$edit_time = $message['time'] - TIME + 600;

if (App::user()->group > $message->user->group || App::user()->group == groups::max() || App::user()->id == $message->theme->id_moderator) {
    $access_edit = true;
} elseif (App::user()->id == $message->user->id && $edit_time > 0) {
    $access_edit = true;
    $doc->msg(__('Для изменения сообщения осталось %d сек', $edit_time));
}

if (!$access_edit) {
    $doc->toReturn();
    $doc->err(__('Сообщение не доступно для редактирования'));
    exit;
}

$doc->title = __('Сообщение от "%s" - редактирование', $message->user->login);

if (isset($_GET['act']) && $_GET['act'] == 'hide') {
    $doc->toReturn(new url('theme.php', ['id' => $message->id_theme]));

    $message->group_show = 2;
    $message->save();

    $doc->msg(__('Сообщение успешно скрыто'));
    exit;
}

if (isset($_GET['act']) && $_GET['act'] == 'show') {
    $doc->toReturn(new url('theme.php', ['id' => $message->id_theme]));

    $message->group_show = 0;
    $message->save();

    $doc->msg(__('Сообщение будет отображаться'));
    exit;
}

if (isset($_POST['message'])) {
    $message_new = text::input_text($_POST['message']);

    if ($message_new == $message->message) {
        $doc->err(__('Изменения не обнаружены'));
    } elseif ($dcms->censure && $mat = is_valid::mat($message_new)) {
        $doc->err(__('Обнаружен мат: %', $mat));
    } elseif ($message_new) {
        $doc->toReturn(new url('theme.php', ['id' => $message['id_theme']]));
        ForumHistory::create([
            'id_message' => $message->id,
            'id_user' => $message->edit_id_user ?? $message->id_user,
            'time' => $message->time,
            'message' => $message->message,
        ]);

        $message->message = $message_new;
        $message->edit_time = TIME;
        $message->edit_id_user = App::user()->id;
        $message->increment('edit_count', 1);
        $message->save();

        $doc->msg(__('Сообщение успешно изменено'));
        exit;
    } else {
        $doc->err(__('Нельзя оставить пустое сообщение'));
    }
}

$form = new form(new url());
$form->textarea('message', __('Редактирование сообщения'), $message->message);
$form->button(__('Применить'));
$form->display();

$doc->act(__('Вложения'),
    'message.files.php?id=' . $message->id . (isset($_GET['return']) ? '&amp;return=' . urlencode($_GET['return']) : null));

if (isset($_GET['return'])) {
    $doc->ret(__('В тему'), text::toValue($_GET['return']));
} else {
    $doc->ret(__('В тему'), 'theme.php?id=' . $message['id_theme']);
}