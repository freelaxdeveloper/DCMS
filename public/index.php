<?php
include_once '../sys/inc/start.php';
use App\{document,widget,ini};
use Dcms\Core\Router;
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
Router::get('', ['use' => 'home@index', 'name' => 'home']);
Router::get('/captcha/(\?captcha_session\=[a-z0-9]+)', ['use' => 'captcha@captcha', 'name' => 'captcha']);

Router::get('/auth/login', ['use' => 'auth@login', 'name' => 'auth:login', 'onlyGroup' => GROUP_GUEST]);
Router::get('/auth/register', ['use' => 'auth@register', 'name' => 'auth:register', 'onlyGroup' => GROUP_GUEST]);
Router::post('/auth/register', ['use' => 'auth@postRegister', 'name' => 'auth:postRegister', 'onlyGroup' => GROUP_GUEST]);
Router::post('/auth/login', ['use' => 'auth@postLogin', 'name' => 'auth:postLogin', 'onlyGroup' => GROUP_GUEST]);

Router::get('/chat_mini', ['use' => 'chatmini@messagesList', 'name' => 'chat']);
Router::post('/chat_mini', ['use' => 'chatmini@send', 'name' => 'chatmini:send', 'group' => GROUP_USER]);
Router::get('/chat_mini/([0-9]+)/delete/(\?token\=[a-z0-9]+)', ['use' => 'chatmini@delete', 'name' => 'chat:delete', 'group' => GROUP_MODER]);
Router::get('/chat_mini/drop/(\?token\=[a-z0-9]+)', ['use' => 'chatmini@drop', 'name' => 'chat:drop', 'group' => GROUP_SMODER]);
Router::get('/chat_mini/actions/([0-9]+)', ['use' => 'chatmini@actions', 'name' => 'chatminiActions']);

Router::get('/user/([0-9]+)/view', ['use' => 'user@view', 'name' => 'user:view']);
Router::get('/user/menu', ['use' => 'user@menu', 'name' => 'user:menu', 'group' => GROUP_USER]);
Router::get('/user/exit/(\?token\=[a-z0-9]+)', ['use' => 'user@exit', 'name' => 'user:exit', 'group' => GROUP_USER]);

Router::dispatch();

exit;

$doc = new document;
$doc->title = __($dcms->title); // локализированое название сайта
$widgets = (array)ini::read(H . '/sys/ini/widgets.ini'); // получаем список виджетов

foreach ($widgets as $widget_name => $show) {
    if (!$show) {
        continue; // если стоит отметка о скрытии, то пропускаем
    }
    $widget = new widget(H . '/sys/widgets/' . $widget_name); // открываем
    $widget->display(); // отображаем
}
