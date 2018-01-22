<?php
$subdomain_theme_redirect_disable = true; // принудительное отключение редиректа на поддомены, соответствующие типу браузера
include_once '../sys/inc/start.php';
use App\{document,cache,cache_aut_failture,captcha,misc,crypt,text,form,url};
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
        } elseif (crypt::hash($password, $dcms->salt) !== $user['password']) {
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
                Authorize::authorized($user->id, crypt::encrypt($user->password, $dcms->salt_user));
                $user->save();
                /* $_SESSION[SESSION_ID_USER] = App::user()->id;
                if (isset($_POST['save_to_cookie']) && $_POST['save_to_cookie']) {
                    setcookie(COOKIE_ID_USER, App::user()->id, TIME + 60 * 60 * 24 * 365);
                    setcookie(COOKIE_USER_PASSWORD, crypt::encrypt($password, $dcms->salt_user), TIME + 60 * 60 * 24 * 365);
                } */
            }
        }
    }
}
if ($user && $user->group) {
    // авторизовались успешно
    // удаляем информацию как о госте
    $res = $db->prepare("DELETE FROM `guest_online` WHERE `ip_long` = ? AND `browser` = ?;");
    $res->execute(Array($dcms->ip_long, $dcms->browser_name));

    /* if (isset($_GET['auth_key']) && cache::get($_GET['auth_key']) === 'request') {
        cache::set($_GET['auth_key'], array('session' => $_SESSION, 'cookie' => $_COOKIE), 60);
    } */

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

if ($dcms->vk_auth_enable && $dcms->vk_app_id && $dcms->vk_app_secret) {
    $vk = new vk($dcms->vk_app_id, $dcms->vk_app_secret);
    $form = new form($vk->getAuthorizationUri('http://' . $_SERVER['HTTP_HOST'] . '/vk.php', 'email'));
    $form->button(__('Вход через %s', 'vk.com'));
    $form->display();
}
