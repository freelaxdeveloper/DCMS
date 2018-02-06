<?php
include_once '../sys/inc/start.php';
use App\{dpanel,document,form};
use App\Models\Dcms;

dpanel::check_access();
$doc = new document(5);
$doc->title = __('Параметры форума');

$profileFields = Dcms::where('key', 'profile_fields')->firstOrCreate(['key' => 'profile_fields']);

if (isset($_POST['save'])) {
    
    $options = $profileFields->options;
    $options[$_POST['key']] = $_POST['title'];
    $profileFields->options = $options;
    $profileFields->save();

    $doc->msg('Настройки сохранены');
}
foreach ($profileFields->options as $key => $title) {
    echo "{$key}: {$title}<br>";
}

$form = new form('?' . passgen());
$form->text('key', __('Ключ'));
$form->text('title', __('Название'));
$form->button(__('Применить'), 'save');
$form->display();

$doc->ret(__('Админка'), '/dpanel/');