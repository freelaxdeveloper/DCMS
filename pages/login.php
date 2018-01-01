<?php
$subdomain_theme_redirect_disable = true; // принудительное отключение редиректа на поддомены, соответствующие типу браузера
include_once '../sys/inc/start.php';
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


if ($user->group) {
    if (isset($_GET['auth_key']) && cache::get($_GET['auth_key']) === 'request') {
        cache::set($_GET['auth_key'], array('session' => $_SESSION, 'cookie' => $_COOKIE), 60);
    }

    $doc->clean();
    header('Location: ' . $return, true, 302);
    exit;
}

$need_of_captcha = cache_aut_failture::get($dcms->ip_long);

if ($need_of_captcha && (empty($_POST['captcha']) || empty($_POST['captcha_session']) || !captcha::check($_POST['captcha'], $_POST['captcha_session']))) {
    $doc->err(__('Проверочное число введено неверно'));
} elseif (isset($_POST['login']) && isset($_POST['password'])) {
    if (!$_POST['login']) $doc->err(__('Введите логин'));
    elseif (!$_POST['password']) $doc->err(__('Введите пароль'));
    else {
        $login = (string) $_POST['login'];
        $password = (string) $_POST['password'];

        $q = $db->prepare("SELECT `id`, `password` FROM `users` WHERE `login` = ? LIMIT 1");
        $q->execute(Array($login));
        if(!$row = $q->fetch()) {
            $doc->err(__('Логин "%s" не зарегистрирован', $login));
        } elseif (crypt::hash($password, $dcms->salt) !== $row['password']) {
            $need_of_captcha = true;
            cache_aut_failture::set($dcms->ip_long, true, 600); // при ошибке заставляем пользователя проходить капчу
            misc::logaut($row['id'], 'post', 0, 0); // пишем в журнал неудачную попытку войти
            $doc->err(__('Вы ошиблись при вводе пароля'));
        } else {
            $user_t = new user((int)$row['id']);
            if(!$user_t->group) {
                $doc->err(__('Ошибка при получении профиля пользователя'));
            } elseif ($user_t->a_code) {
                $doc->err(__('Аккаунт не активирован'));
                misc::logaut($user_t->id, 'post', 0, 0);
            } else {
                $user = $user_t;
                cache_aut_failture::set($dcms->ip_long, false, 1);
                misc::logaut($user->id, 'post', 1); // в журнал 

                if ($user->recovery_password) {
                    // если пользователь авторизовался, то ключ для восстановления ему больше не нужен
                    $user->recovery_password = '';
                }
                $_SESSION[SESSION_ID_USER] = $user->id;
                if (isset($_POST['save_to_cookie']) && $_POST['save_to_cookie']) {
                    setcookie(COOKIE_ID_USER, $user->id, TIME + 60 * 60 * 24 * 365);
                    setcookie(COOKIE_USER_PASSWORD, crypt::encrypt($password, $dcms->salt_user), TIME + 60 * 60 * 24 * 365);
                }
            }
        }
    }
} elseif (!empty($_COOKIE[COOKIE_ID_USER]) && !empty($_COOKIE[COOKIE_USER_PASSWORD])) {
    $tmp_user = new user($_COOKIE[COOKIE_ID_USER]);

    if (crypt::hash(crypt::decrypt($_COOKIE[COOKIE_USER_PASSWORD], $dcms->salt_user), $dcms->salt) === $tmp_user->password) {
         misc::logaut($tmp_user->id, 'cookie', 1); // пишем в журнал успешную авторизацию 
        $user = $tmp_user;
        $_SESSION[SESSION_ID_USER] = $user->id;
    } else {
        $need_of_captcha = true;
        cache_aut_failture::set($dcms->ip_long, true, 600); // при ошибке заставляем пользователя проходить капчу
        misc::logaut($tmp_user->id, 'cookie', 0); // пишем в журнал попытку входа по куках
        setcookie(COOKIE_ID_USER);
        setcookie(COOKIE_USER_PASSWORD);
    }
}

if ($user->group) {
    // авторизовались успешно
    // удаляем информацию как о госте
    $res = $db->prepare("DELETE FROM `guest_online` WHERE `ip_long` = ? AND `browser` = ?;");
    $res->execute(Array($dcms->ip_long, $dcms->browser_name));

    if (isset($_GET['auth_key']) && cache::get($_GET['auth_key']) === 'request') {
        cache::set($_GET['auth_key'], array('session' => $_SESSION, 'cookie' => $_COOKIE), 60);
    }

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
$form->checkbox('save_to_cookie', __('Запомнить меня'));
if ($need_of_captcha) $form->captcha();
$form->button(__('Авторизация'));
$form->display();

if ($dcms->vk_auth_enable && $dcms->vk_app_id && $dcms->vk_app_secret) {
    $vk = new vk($dcms->vk_app_id, $dcms->vk_app_secret);
    $form = new form($vk->getAuthorizationUri('http://' . $_SERVER['HTTP_HOST'] . '/vk.php', 'email'));
    $form->button(__('Вход через %s', 'vk.com'));
    $form->display();
}
