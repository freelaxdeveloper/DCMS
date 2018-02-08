<?php
include_once '../sys/inc/start.php';
use App\{document,widget,ini};
use Dcms\Core\Router as R;
# доступные языки, используется в роутах
define('AVAILABLE_LANG', '(uk|en|ru|ko)');
define('GROUP_CREATOR', 6);
define('GROUP_ADMIN', 5);
define('GROUP_SMODER', 3);
define('GROUP_MODER', 2);
define('GROUP_USER', 1);
define('GROUP_GUEST', 0);
/* 
// ждем именно это, другое не надо
$only = [
    'last_name', 
    'first_name', 
    'sex',
];
// предполагается что пользователь написал всякий бред в имена полей
$_POST = [
    'first_name' => 'Bober', 
    'last_name' => 'Bob', 
    'sex' => 'male',
];
$response = array_combine($only, $_POST);

dd($response);
 */
R::get('', [
    'use' => 'home@index', 
    'name' => 'home',
]);
R::get('/captcha/(\?captcha_session\=[a-z0-9]+)', [
    'use' => 'captcha@captcha', 
    'name' => 'captcha',
]);

R::get('/auth/login', [
    'use' => 'auth@login', 
    'name' => 'auth:login', 
    'onlyGroup' => GROUP_GUEST,
]);
R::get('/auth/register', [
    'use' => 'auth@register', 
    'name' => 'auth:register', 
    'onlyGroup' => GROUP_GUEST,
]);
R::post('/auth/register', [
    'use' => 'auth@postRegister', 
    'name' => 'auth:postRegister', 
    'onlyGroup' => GROUP_GUEST,
]);
R::post('/auth/login', [
    'use' => 'auth@postLogin', 
    'name' => 'auth:postLogin', 
    'onlyGroup' => GROUP_GUEST
]);

R::get('/chat_mini', [
    'use' => 'chatmini@messagesList', 
    'name' => 'chat',
]);
R::post('/chat_mini', [
    'use' => 'chatmini@send', 
    'name' => 'chatmini:send', 
    'group' => GROUP_USER,
]);
R::get('/chat_mini/([0-9]+)/delete/(\?token\=[a-z0-9]+)', [
    'use' => 'chatmini@delete', 
    'name' => 'chat:delete', 
    'group' => GROUP_MODER,
]);
R::get('/chat_mini/drop/(\?token\=[a-z0-9]+)', [
    'use' => 'chatmini@drop', 
    'name' => 'chat:drop', 
    'group' => GROUP_SMODER,
]);
R::get('/chat_mini/actions/([0-9]+)', [
    'use' => 'chatmini@actions', 
    'name' => 'chatminiActions',
]);

R::get('/user/([0-9]+)/view', [
    'use' => 'user@view', 
    'name' => 'user:view',
]);
R::get('/user/menu', [
    'use' => 'user@menu', 
    'name' => 'user:menu', 
    'group' => GROUP_USER,
]);
R::get('/user/exit/(\?token\=[a-z0-9]+)', [
    'use' => 'user@exit', 
    'name' => 'user:exit', 
    'group' => GROUP_USER,
]);

R::dispatch();
