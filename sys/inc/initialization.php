<?php

/**
 * @const TIME_START Время запуска скрипта в миллисекундах
 */
define('TIME_START', microtime(true)); // время запуска скрипта

/**
 * @const DCMS Метка о DCMS
 */
define('DCMS', true);

/**
 * @const AJAX скрипт вызван AJAX запросом
 */
define('AJAX', strtolower(@$_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

/**
 * @const IS_WINDOWS Запущено ли на винде
 */
if (!defined('IS_WINDOWS')) {
    define('IS_WINDOWS', strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
}

// устанавливаем Московскую временную зону по умолчанию
if (@function_exists('ini_set')) {
    ini_set('date.timezone', 'Europe/Moscow');
}

/**
 * @const H путь к корневой директории сайта
 */
if (!defined('H')) {
    define('H', $_SERVER ['DOCUMENT_ROOT']); // корневая директория сайта
}

/**
 * @const TEMP временная папка
 */
define('TEMP', H . '/sys/tmp');
/**
 * @const TMP временная папка
 */
define('TMP', H . '/sys/tmp');
/**
 * @const FILES Путь к папке загруз-центра
 */
define('FILES', realpath(H . '/sys/files'));
/**
 * @const TIME UNIXTIMESTAMP 
 */
define('TIME', time());
/**
 * @const DAY_TIME UNIXTIMESTAMP на начало текущих суток
 */
define('DAY_TIME', mktime(0, 0, 0));
/**
 * @const IS_MAIN true, если мы на главной странице
 */
define('IS_MAIN', $_SERVER['SCRIPT_NAME'] == '/index.php');
/**
 * @const SESSION_LIFE_TIME время жизни сессии, а также время последней активности пользователей, считающихся онлайн
 */
define('SESSION_LIFE_TIME', 600);
/**
 * @const SESSION_NAME имя сессии
 */
define('SESSION_NAME', 'DCMS_SESSION');
/**
 * @const SESSION_ID_USER ключ сессий, в котором хранится идентификатор пользователя
 */
define('SESSION_ID_USER', 'DCMS_SESSION_ID_USER');
/**
 * @const SESSION_PASSWORD_USER ключ сессий, в котором хранится пароль пользователя
 */
define('SESSION_PASSWORD_USER', 'DCMS_SESSION_PASSWORD_USER');
/**
 * @const COOKIE_ID_USER идентификатор пользователя в COOKIE
 */
define('COOKIE_ID_USER', 'DCMS_COOKIE_ID_USER');
/**
 * @const COOKIE_USER_PASSWORD пароль пользователя в COOKIE
 */
define('COOKIE_USER_PASSWORD', 'DCMS_COOKIE_USER_PASSWORD');
/**
 * @const SPRITE_CLASS_PREFIX префикс для css класса со спрайтами иконок
 */
define('SPRITE_CLASS_PREFIX', 'DCMS_');


if (@function_exists('ini_set')) {
    // время жизни сессии
    ini_set('session.cache_expire', SESSION_LIFE_TIME);

    // игнорировать повторяющиеся ошибки
    ini_set('ignore_repeated_errors', true);

    // показываем только фатальные ошибки
    ini_set('error_reporting', E_ERROR);

    // непосредственно, включаем показ ошибок
    ini_set('display_errors', true);
}

/**
 * @const URL текущая страница.
 */
define('URL', urlencode($_SERVER ['REQUEST_URI']));

if (function_exists('mb_internal_encoding')) {
    // Выставляем кодировку для mb_string  
    mb_internal_encoding('UTF-8');
}

if (function_exists('iconv')) {
    // Выставляем кодировку для Iconv
    iconv_set_encoding('internal_encoding', 'UTF-8');
}

/**
 * автоматическая загрузка классов
 * @param string $class_name имя класса
 */
/*
function dcmsAutoload($class_name) {
    $path = H . '/sys/plugins/classes/' . strtolower($class_name) . '.class.php';
    if (file_exists($path)) {
        include_once ($path);
    }
}

spl_autoload_register('dcmsAutoload');
*/
require_once(H . '/vendor/autoload.php');
include_once (H . '/sys/plugins/classes/cache.class.php');

/**
 * Генератор пароля
 * @param int $len Длина пароля
 * @return string
 */
function passgen($len = 32) {
    $password = '';
    $small = 'abcdefghijklmnopqrstuvwxyz';
    $large = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numbers = '1234567890';
    for ($i = 0; $i < $len; $i++) {
        switch (mt_rand(1, 3)) {
            case 3 :
                $password .= $large [mt_rand(0, 25)];
                break;
            case 2 :
                $password .= $small [mt_rand(0, 25)];
                break;
            case 1 :
                $password .= $numbers [mt_rand(0, 9)];
                break;
        }
    }
    return $password;
}
use App\language_pack;
/**
 * @global \language_pack $user_language_pack Текущий языковой пакет
 */
$user_language_pack = new language_pack(false);

/**
 * Локализация текстовой строки.
 * ВНИМАНИЕ!!! не использовать динамические строки
 * @global language_pack $user_language_pack
 * @return string Локализованная строка
 */
function __() {
    $args = func_get_args();
    $args_num = count($args);
    if (!$args_num) {
        // нет ни строки ни параметров, вообще нихрена
        return '';
    }

    global $user_language_pack;
    $string = $user_language_pack->getString($args[0]);

    if ($args_num == 1) {
        // строка без параметров
        return $string;
    }

// строка с параметрами
    $args4eval = array();
    for ($i = 1; $i < $args_num; $i++) {
        $args4eval[] = '$args[' . $i . ']';
    }
    return eval('return sprintf($string,' . implode(',', $args4eval) . ');');
}

@session_name(SESSION_NAME) or die(__('Невозможно инициализировать сессии'));
@session_start() or die(__('Невозможно инициализировать сессии'));
/**
 * @const SESS Идентификатор сессии
 */
define('SESS', preg_replace('#[^a-z0-9]#i', '', session_id()));