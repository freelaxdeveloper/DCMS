<?php
include_once '../sys/inc/start.php';
use App\{document,languages,language_pack,listing,url};
use App\App\App;

$doc = new document(); // инициализация документа для браузера
$doc->title = __('Настройки языка');


if (!empty($_GET['set_lang'])) {
    if (!languages::exists($_GET['set_lang'])) {
        $doc->err(__('Запрашиваемый языковой пакет не найден'));
    } else {
        $user_language_pack = new language_pack($_GET['set_lang']);

        if (App::user()->group) {
            App::user()->language = $user_language_pack->code;
        } else {
            $_SESSION['language'] = $user_language_pack->code;
        }

        $doc->msg(__('Языковой пакет %s (%s) успешно выбран', $user_language_pack->name, $user_language_pack->enname));
        $doc->toReturn();
        exit;
    }
}



$languages = languages::getList();
$listing = new listing();
foreach ($languages as $key => $l) {
    $post = $listing->post();
    $post->setUrl(new url(null, array('set_lang' => $key)));
    $post->title = $user_language_pack->code == $key ? $l['name'] : $l['enname'];
    $post->icon = empty($l['icon']) ? false : $l['icon'];
}
$listing->display(__('Языковые пакеты не найдены'));

$doc->ret(__('Личное меню'), '/menu.user.php');
