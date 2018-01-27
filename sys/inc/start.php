<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

// Проверяем версию PHP
version_compare(PHP_VERSION, '7.0', '>=') or die('Требуется PHP >= 7.0');

/**
 * Константы и функции, необходимые для работы движка.
 * Выделены в отдельный файл чтобы избежать дублирования кода в инсталляторе
 */
require_once dirname(__FILE__) . '/initialization.php';
use App\{cache_events,dcms,languages,language_pack,DB,mail,log_of_referers,log_of_visits,browser,user,misc,groups};
use Jenssegers\Blade\Blade;
use App\App\App;
/**
 * во время автоматического обновления не должно быть запросов со стороны пользователя
 */
if (cache_events::get('system.update.work')) {
    exit('Выполняется обновление системы. Пожалуйста, обновите страницу позже.');
}

/**
 * @const USER_AGENT
 */
if(!defined('USER_AGENT'))
    define ('USER_AGENT', @$_SERVER['HTTP_USER_AGENT']);

/**
 * загрузка системных параметров
 * @global \dcms $dcms Основной объект системы
 */
$dcms = dcms::getInstance();

/**
 *  проверка доступности поддомена.
 *  используется при включении поддомена для определенного типа браузера
 */
if (isset($_GET['check_domain_work'])) {
    echo $dcms->check_domain_work;
    exit;
}

if ((empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') &&
    (empty($_SERVER['HTTP_X_FORWARDED_PROTO']) || $_SERVER['HTTP_X_FORWARDED_PROTO'] !== 'https') &&
    (empty($_SERVER['HTTP_X_FORWARDED_SSL']) || $_SERVER['HTTP_X_FORWARDED_SSL'] !== 'on')
) {
    if ($dcms->https_only) {
        // принудительная переадресация на https
        header("Location: https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
        exit;
    }
} else if ($dcms->https_hsts) {
    // если пользователь уже зашел по https, то говорим браузеру, чтоб он больше не обращался по http
    header("Strict-Transport-Security: max-age=31536000"); // https://ru.wikipedia.org/wiki/HSTS
}

/**
 * переадресация на поддомен, соответствующий типу браузера
 */
if ($dcms->subdomain_theme_redirect && empty($subdomain_theme_redirect_disable)) {
    if ($_SERVER['HTTP_HOST'] === $dcms->subdomain_main) {
        // проверяем, что мы находимся на главном домене, а не на поддомене
        // свойство, в котором хранится значение поддомена для данного типа браузера
        $subdomain_var = "subdomain_" . $dcms->browser_type_auto;
        // свойство, в котором хранится парметр, отвечающий за работоспособность домена
        $subdomain_enable = "subdomain_" . $dcms->browser_type_auto . "_enable";

        if ($dcms->$subdomain_enable) {
            // проверяем, включен ли поддомен для данного типа браузера
            // переадресовываем на соответствующий поддомен
            header('Location: //' . $dcms->$subdomain_var . '.' . $dcms->subdomain_main . $_SERVER ['REQUEST_URI']);
            exit;
        }
    }
}

if (!empty($_SESSION['language']) && languages::exists($_SESSION['language'])) {
    // языковой пакет из сессии
    $user_language_pack = new language_pack($_SESSION['language']);
} else if ($dcms->language && languages::exists($dcms->language)) {
    // системный языковой пакет
    $user_language_pack = new language_pack($dcms->language);
}

// этот параметр будут влиять на счетчики
if ($dcms->new_time_as_date) {
    // новые файлы, темы и т.д. будут отображаться за текущее число
    define('NEW_TIME', DAY_TIME);
} else {
    // новые файлы, темы и т.д. будут отображаться за последние 24 часа
    define('NEW_TIME', TIME - 86400);
}
use Illuminate\Database\Capsule\Manager as Capsule;
try {
    $db = DB::me($dcms->mysql_host, $dcms->mysql_base, $dcms->mysql_user, $dcms->mysql_pass);

    $capsule = new Capsule;
    $capsule->addConnection([
        'driver'    => 'mysql',
        'host'      => $dcms->mysql_host,
        'database'  => $dcms->mysql_base,
        'username'  => $dcms->mysql_user,
        'password'  => $dcms->mysql_pass,
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ]);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

} catch (\Exception $e) {
    die('Ошибка подключения к базе данных:' . $e->getMessage());
}

if ($_SERVER['SCRIPT_NAME'] != '/sys/cron.php') {
    /**
     * Поэтапная отправка писем из очереди
     */
    mail::queue_process();

    /**
     * Запись переходов со сторонних сайтов
     * @global \log_of_referers $log_of_referers
     */
    if ($dcms->log_of_referers) {
        $log_of_referers = new log_of_referers();
    }

    /**
     * Запись посещений
     * @global log_of_visits $log_of_visits
     */
    if ($dcms->log_of_visits) {
        $log_of_visits = new log_of_visits();
    }

    /**
     * обработка данных пользователя
     */
    if (App::user()->id !== false) {
        App::user()->last_visit = TIME; // запись последнего посещения
        if (AJAX) {
            // при AJAX запросе только обновляем сведения о времени последнего посещения, чтобы пользователь оставался в онлайне
            $res = $db->prepare("UPDATE `users_online` SET `time_last` = ? WHERE `id_user` = ? LIMIT 1");
            $res->execute(Array(TIME, App::user()->id));
        } else {

            App::user()->conversions++; // счетчик переходов

            $q = $db->prepare("SELECT * FROM `users_online` WHERE `id_user` = ? LIMIT 1");
            $q->execute(Array(App::user()->id));
            if ($q->fetch()) {
                $res = $db->prepare("UPDATE `users_online` SET `conversions` = `conversions` + '1' , `time_last` = ?, `id_browser` = ?, `ip_long` = ?, `request` = ? WHERE `id_user` = ? LIMIT 1");
                $res->execute(Array(TIME, $dcms->browser_id, $dcms->ip_long, $_SERVER ['REQUEST_URI'], App::user()->id));
            } else {
                $res = $db->prepare("INSERT INTO `users_online` (`id_user`, `time_last`, `time_login`, `request`, `id_browser`, `ip_long`) VALUES (?, ?, ?, ?, ?, ?)");
                $res->execute(Array(App::user()->id, TIME, TIME, $_SERVER ['REQUEST_URI'], $dcms->browser_id, $dcms->ip_long));
                App::user()->count_visit++; // счетчик посещений
            }
        }
    } else {
        // обработка гостя
        // зачистка гостей, вышедших из онлайна
        $time_last = TIME - SESSION_LIFE_TIME;
        $res = $db->prepare("DELETE FROM `guest_online` WHERE `time_last` < ?");
        $res->execute(Array($time_last));

        if (!AJAX) {
            // при ajax запросе данные о переходе засчитывать не будем

            $q = $db->prepare("SELECT * FROM `guest_online` WHERE `ip_long` = ? AND `browser_ua` = ? LIMIT 1");
            $q->execute(Array($dcms->ip_long, (string) USER_AGENT));
            if ($q->fetch()) {
                // повторные переходы гостя
                $res = $db->prepare("UPDATE `guest_online` SET `time_last` = ?, `request` = ?, `is_robot` = ?, `domain` = ?, `conversions` = `conversions` + 1 WHERE  `ip_long` = ? AND `browser_ua` = ? LIMIT 1");
                $res->execute(Array(TIME, $_SERVER ['REQUEST_URI'], browser::getIsRobot() ? '1' : '0', $_SERVER['HTTP_HOST'], $dcms->ip_long, (string) USER_AGENT));
            } else {
                // новый гость
                $res = $db->prepare("INSERT INTO `guest_online` (`ip_long`, `browser`, `browser_ua`, `time_last`, `time_start`, `domain`, `request`, `is_robot` ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $res->execute(Array($dcms->ip_long, $dcms->browser_name, (string) USER_AGENT, TIME, TIME, $dcms->subdomain_main, $_SERVER ['REQUEST_URI'], browser::getIsRobot() ? '1' : '0'));
            }
        }
    }

    $cron_time = cache_events::get('cron');
    if ($cron_time < TIME - 180) {
        misc::log('cron не настроен на сервере. вызываем вручную', 'cron');
        include H . '/sys/cron.php';
    }
    unset($cron_time);

    /**
     * при полном бане никуда кроме страницы бана нельзя
     */
    if (App::user()->is_ban_full && $_SERVER['SCRIPT_NAME'] != '/pages/ban.php') {
        header('Location: /ban.php?' . SID);
        exit;
    }

    /**
     * включаем полный показ ошибок для создателя, если включено в админке
     */
    #if ($dcms->debug && App::user()->group == groups::max() && @function_exists('ini_set')) {
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', true);
    #}

    /**
     * пользовательский языковой пакет
     */
    if (App::user()->group && App::user()->language != $user_language_pack->code && languages::exists(App::user()->language)) {
        $user_language_pack = new language_pack(App::user()->language);
    }
}

function view(string $template, array $params = [], bool $view = true)
{
    static $blade;
    if (!$blade) {
        $blade = new Blade(H . '/sys/themes/default/blade', H . '/sys/themes/default/cache');

    }
    $blade->compiler()->directive('__', function ($text) {
        return "<?= __($text) ?>";
    });
    $blade->compiler()->directive('toOutput', function ($text) {
        return "<?= App\\text::toOutput($text) ?>";
    });
    if ($view) {
        echo $blade->make($template, $params);
    } else {
        return $blade->make($template, $params);
    }
    
}
function dd($array, bool $exit = true): void
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
    if ($exit)
        exit;
}
function redirect(string $path = '/'): void
{
    header('Location: ' . $path);
    exit;
}
function refresh(string $path = '/'): void
{
    header('Refresh:1; ' . $path);
    exit;
}
function elixir(string $path): string
{
    $dir = '/public/build/';
    $manifest = file_get_contents(H . $dir . 'rev-manifest.json');
    $manifest = json_decode($manifest);
    if (empty($manifest->$path)) {
        throw new \Exception("File# {$path} not exists");
    }
    $filePath = $dir . $manifest->$path;
    return $filePath;
}
