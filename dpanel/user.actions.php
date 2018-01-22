<?php
include_once '../sys/inc/start.php';
use App\{document,menu_ini};
use App\Models\User;
use App\App\App;

$doc = new document(2);
$doc->title = __('Действия');

$user_actions = new menu_ini('user_actions');

if (isset($_GET['id']))
    $ank = User::find($_GET['id']);
else
    $ank = App::user();

if (!$ank->group) {
    $doc->toReturn();
    $doc->err(__('Нет данных'));
    exit;
}

$doc->title .= ' "' . $ank->login . '"';

$user_actions->value_add('id', $ank->id);

if ($ank->group >= App::user()->group) {
    $doc->toReturn();
    $doc->err(__('Ваш статус не позволяет производить действия с данным пользователем'));
    exit;
}

$user_actions->display();

$doc->ret(__('Анкета "%s"', $ank->login), '/profile.view.php?id=' . $ank->id);
$doc->ret(__('Админка'), '/dpanel/');