<?php
include_once '../sys/inc/start.php';
use App\{document,widget,ini};
use Requests;

$headers = ['Accept' => 'application/json'];
$options = ['auth' => ['test', '123']];
$request = Requests::post('http://dcms.local/api/user.php', $headers, ['user_id' => 1], $options);

$user = json_decode($request->body);
dd($user);