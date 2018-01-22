<?php
$subdomain_theme_redirect_disable = true; // принудительное отключение редиректа на поддомены, соответствующие типу браузера
include_once '../sys/inc/start.php';
use App\{document,is_valid,crypt,design,mail,form,url,vk,captcha,antiflood};
use App\Models\User;
use App\App\{App, Authorize};

$doc = new document();
$doc->title = __('Регистрация');
if (App::user()->group) {
    $doc->access_denied(__('Вы уже зарегистрированы'));
}

if (!$dcms->reg_open) {
    $doc->access_denied(__('Регистрация временно закрыта'));
}
if (isset($_POST['post'])) {
    $v = new Valitron\Validator($_POST);
    $v->rule('required', ['captcha', 'captcha_session', 'password', 'password_retry', 'login', 'token', 'sex'])->message('{field} - обязательно для заполнения');
    $v->rule('equals', 'password', 'password_retry')->message('Пароли не совпадают');
    $v->rule('slug', 'login')->message('Логин содержит запрещенные символы');
    $v->rule('lengthBetween', 'password', 6, 32)->message('Пароль должен состоять от 6 до 32 символов');
    $v->rule('in', 'sex', [0, 1])->message('Ошибка выбора пола');
    $v->labels([
        'password' => 'Пароль',
        'password_retry' => 'Подтверждение пароля',
        'login' => 'Логин',
        'sex' => 'Пол',
        'captcha' => 'Проверочное число',
        'captcha_session' => 'Проверочное число',
    ]);
    if (!captcha::check($_POST['captcha'], $_POST['captcha_session'])) {
        $v->error('error_captcha', 'Проверочное число введено неверно');
    }
    if (!antiflood::useToken($_POST['token'], 'register')) {
        $v->error('error_token', 'Ошибка регистрации');
    }
    if (User::where('login', $_POST['login'])->first()) {
        $v->error('error_login', 'Пользователь с таким логином уже зарегистрирован');
    }
    $v->validate();
    $doc->err($v->errors());
    if ($v->validate()) {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $newUser = User::create([
            'login' => $_POST['login'],
            'password' => $hash,
            'sex' => $_POST['sex'],
            'reg_date' => TIME,
        ]);
        Authorize::authorized($newUser->id, $newUser->password);
        $doc->msg('Регистрация успешна');
    }
}
$form = new form('?' . passgen());
$form->hidden('token', antiflood::getToken('register'));
$form->text('login', __('Выберите ник'));
$form->bbcode('- ' . __('Сочетание русского и английского алфавитов запрещено'));
$form->bbcode('- ' . __('Использование пробелов вначале и конце строк запрещено'));
$form->bbcode('- ' . __('Ник не должен начинаться с цифр'));
$form->password('password', __('Пароль'));
$form->password('password_retry', __('Повторите пароль'));
$form->select('sex', __('Ваш пол'), [[1, __('Мужской')], [0, __('Женский')]]);
$form->captcha();
$form->button(__('Зарегистрироваться'), 'post', false);
$form->display();