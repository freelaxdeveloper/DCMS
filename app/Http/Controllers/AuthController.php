<?php
namespace Dcms\Http\Controllers;

use Dcms\Core\Controller;
use App\{document,is_valid,crypt,design,mail,form,url,captcha,antiflood,cache_aut_failture};
use Dcms\Models\{User,GuestOnline};
use App\App\{App,Authorize};

class AuthController extends Controller{

    public function login()
    {
        global $dcms;

        $need_of_captcha = cache_aut_failture::get($dcms->ip_long);

        $this->doc->title = __('Авторизация');

        $form = new form(route('auth:postLogin'));
        $form->hidden('token', antiflood::getToken('login'));
        $form->input('login', __('Логин'));
        $form->password('password', __('Пароль') . ' [' . '[url=/pass.php]' . __('забыли') . '[/url]]');
        if ($need_of_captcha) $form->captcha();
        $form->button(__('Авторизация'));
        $form->display();
    }

    public function register()
    {
        $this->doc->title = __('Регистрация');

        $form = new form(route('auth:postRegister'));
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
    }

    public function postLogin()
    {
        global $dcms;
        $need_of_captcha = cache_aut_failture::get($dcms->ip_long);

        $v = new \Valitron\Validator($_POST);
        $v->rule('required', ['login', 'password', 'token'])->message('{field} - обязательно для заполнения');
        if ($need_of_captcha) {
            $v->rule('required', ['captcha', 'captcha_session'])->message('{field} - обязательно для заполнения');
        }
        $v->labels([
            'password' => 'Пароль',
            'token' => 'Токен',
            'login' => 'Логин',
            'captcha' => 'Капча',
        ]);
        if ($need_of_captcha && !captcha::check($_POST['captcha'], $_POST['captcha_session'])) {
            $v->error('error_captcha', 'Проверочное число введено неверно');
        }
        if (!antiflood::useToken($_POST['token'], 'login')) {
            $v->error('error_token', 'Ошибка авторизации');
        }
        if (!$user = User::where('login', $_POST['login'])->first()) {
            $v->error('error_login', 'Ошибка авторизации');
        }
        if (!password_verify($_POST['password'], $user->password)) {
            $v->error('error_login', 'Ошибка авторизации');
        }
        if (!$v->validate()) {
            cache_aut_failture::set($dcms->ip_long, true, 600); // при ошибке заставляем пользователя проходить капчу
            return redirect()->back()->with('err', $v->errors());
        }
        $user->token = App::generateToken();
        if (password_needs_rehash($user->password, PASSWORD_DEFAULT)) {
            $user->password = password_hash($password, PASSWORD_DEFAULT);
        }
        $user->save();
        Authorize::authorized($user);

        $guestOnline = GuestOnline::where([
            'ip_long' => $dcms->ip_long, 'browser' => $dcms->browser_name
        ])->first();
        $guestOnline->delete();

        return redirect()->route('home')->with('msg', 'Вы успешно авторизовались');
    }
    public function postRegister()
    {
        $v = new \Valitron\Validator($_POST);
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
        if (!$v->validate()) {
            return redirect()->back()->with('err', $v->errors());
        }

        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $newUser = User::create([
            'login' => $_POST['login'],
            'password' => $hash,
            'sex' => $_POST['sex'],
            'reg_date' => TIME,
            'token' => App::generateToken(),
            'url_token' => App::generateToken(),
        ]);
        Authorize::authorized($newUser);
        return redirect()->route('home')->with('msg', 'Регистрация успешна');
    }
}