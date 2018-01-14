<?php

include_once '../sys/inc/start.php';
use App\{document,text};
use App\Models\NewsComment;

$doc = new document(2);
$doc->title = __('Удаление комментария');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $doc->toReturn();
    $doc->err(__('Ошибка выбора комментария'));
    exit;
}
$id_news = (int) $_GET['id'];

if (!$comment = NewsComment::find($id_news)) {
    $doc->toReturn();
    $doc->err(__('Комментарий не найден'));
    exit;
}

$comment->delete();
$res->execute(Array($id_message));
$doc->msg(__('Комментарий успешно удален'));

$doc->toReturn();

if (isset($_GET['return']))
    $doc->ret(__('Вернуться'), text::toValue($_GET['return']));
else
    $doc->ret(__('Вернуться'), './');