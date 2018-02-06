<?php
include_once '../sys/inc/start.php';
use App\App\App;
use App\Models\User;

App::http_auth();

$v = new Valitron\Validator($_POST);
$v->rule('required', 'user_id');
$v->rule('numeric', 'user_id');
if (!$v->validate()) {
    exit;
}
$user = User::findOrFail($_POST['user_id']);
echo json_encode($user);