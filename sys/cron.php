<?php
define('H', $_SERVER['OLDPWD']);
define('TEMP', H . '/sys/temp/');
define('TIME', time());

require_once H . '/vendor/autoload.php';

use App\{cache,misc,dcms};
use App\Models\User;
use Illuminate\Database\Capsule\Manager as Capsule;

$dcms = dcms::getInstance();
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

$user = User::first();

echo $user->login;


file_put_contents(H . '/test.txt', ':-( ' . date("H:i:s"));

function execute_cron_file($path)
{
    global $db, $dcms, $log_of_visits;
    require $path;
}

if (!cache::get('cron')) {
    cache::set('cron', TIME, 10);
    
    misc::log('CRON start', 'cron');

    $cron_files = (array) @glob(H.'/sys/inc/cron/*.php');
    foreach ($cron_files as $path) {
        $name = basename($path, '.php');
        misc::log('start - '.$name, 'cron');
        execute_cron_file($path);
        misc::log('end - '.$name, 'cron');
        misc::log('-----------------------'."\r\n", 'cron');
    }
    
    misc::log('CRON finish'."\r\n"."\r\n", 'cron');
}
