<?php
$subdomain_theme_redirect_disable = true; // принудительное отключение редиректа на поддомены, соответствующие типу браузера
include_once '../sys/inc/start.php';
use App\{document,cache,cache_aut_failture,captcha,misc,text,form,url};
use App\App\{Authorize,App};
use App\Models\User;

$doc = new document();
$doc->title = __('Авторизация');

if (isset($_GET['redirected_from']) && in_array($_GET['redirected_from'], array('light', 'pda', 'mobile', 'full'))) {
    $subdomain_var = "subdomain_" . $_GET['redirected_from'];
    if (isset($_GET['return'])) {
        $return = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . '://' . $dcms->$subdomain_var . '.' . $dcms->subdomain_main . '/login.php?login_from_cookie&return=' . urlencode($_GET['return']);
    } else {
        $return = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . '://' . $dcms->$subdomain_var . '.' . $dcms->subdomain_main . '/login.php?login_from_cookie&return=' . urlencode('/');
    }
} else {
    if (isset($_GET['return']) && !preg_match('/exit/', $_GET['return'])) {
        $return = $_GET['return'];
    } else {
        $return = '/';
    }
}
if (App::user()->group) {
    $doc->clean();
    header('Location: ' . $return, true, 302);
    exit;
}

$need_of_captcha = cache_aut_failture::get($dcms->ip_long);

$user = false;
if ($need_of_captcha && (empty($_POST['captcha']) || empty($_POST['captcha_session']) || !captcha::check($_POST['captcha'], $_POST['captcha_session']))) {
    $doc->err(__('Проверочное число введено неверно'));
} elseif (isset($_POST['login']) && isset($_POST['password'])) {
    if (!$_POST['login']) $doc->err(__('Введите логин'));
    elseif (!$_POST['password']) $doc->err(__('Введите пароль'));
    else {
        $login = (string) $_POST['login'];
        $password = (string) $_POST['password'];

        if(!$user = User::where('login', $login)->first()) {
            $doc->err(__('Логин "%s" не зарегистрирован', $login));
        } elseif (!password_verify($password, $user->password)) {
            $need_of_captcha = true;
            cache_aut_failture::set($dcms->ip_long, true, 600); // при ошибке заставляем пользователя проходить капчу
            misc::logaut($user['id'], 'post', 0, 0); // пишем в журнал неудачную попытку войти
            $doc->err(__('Вы ошиблись при вводе пароля'));
        } else {
            if(!$user->group) {
                $doc->err(__('Ошибка при получении профиля пользователя'));
            } elseif ($user->a_code) {
                $doc->err(__('Аккаунт не активирован'));
                misc::logaut($user->id, 'post', 0, 0);
            } else {
                cache_aut_failture::set($dcms->ip_long, false, 1);
                misc::logaut($user->id, 'post', 1); // в журнал 

                if ($user->recovery_password) {
                    // если пользователь авторизовался, то ключ для восстановления ему больше не нужен
                    $user->recovery_password = '';
                }
                $user->token = App::generateToken();

                if (password_needs_rehash($user->password, PASSWORD_DEFAULT)) {
                    $user->password = password_hash($password, PASSWORD_DEFAULT);
                }
                $user->save();
                Authorize::authorized($user);
            }
        }
    }
}
if ($user && $user->group) {
    // авторизовались успешно
    // удаляем информацию как о госте
    $res = $db->prepare("DELETE FROM `guest_online` WHERE `ip_long` = ? AND `browser` = ?;");
    $res->execute(Array($dcms->ip_long, $dcms->browser_name));

    $doc->clean();
    header('Location: ' . $return, true, 302);
    exit;
}

if (isset($_GET['return'])) {
    $doc->ret(__('Вернуться'), text::toValue($return));
}

$form = new form(new url(null, array('return' => $return)));
$form->input('login', __('Логин'));
$form->password('password', __('Пароль') . ' [' . '[url=/pass.php]' . __('забыли') . '[/url]]');
//$form->checkbox('save_to_cookie', __('Запомнить меня'));
if ($need_of_captcha) $form->captcha();
$form->button(__('Авторизация'));
$form->display();