<?php

include_once '../sys/inc/start.php';
use App\{document,user,text};
use App\Models\ChatMini;

$doc = new document(2);
$doc->title = __('Удаление сообщения');

if (!isset($_GET ['id']) || !is_numeric($_GET ['id'])) {
    $doc->toReturn('./');
    $doc->err(__('Ошибка выбора сообщения'));
    exit();
}
$id_message = (int) $_GET ['id'];

if (!$message = ChatMini::find($id_message)) {
    $doc->toReturn('./');
    $doc->err(__('Сообщение не найдено'));
    exit();
}
$message->delete();

$doc->msg(__('Сообщение успешно удалено'));

$ank = new user($message ['id_user']);

$dcms->log('Мини чат', "Удаление сообщения от [url=/profile.view.php?id={$ank->id}]{$ank->login}[/url] ([when]$message[time][/when]):\n" . $message ['message']);

$doc->toReturn('./');
if (isset($_GET ['return']))
    $doc->ret(__('Вернуться'), text::toValue($_GET ['return']));
else
    $doc->ret(__('Вернуться'), './');
?>