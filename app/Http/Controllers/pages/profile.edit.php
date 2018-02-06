<?php

include_once '../sys/inc/start.php';
use App\{document,is_valid,text,form};
use App\App\App;
use App\Models\Dcms;

$doc = new document(1);
$doc->title = __('Мой профиль');

$profileFields = Dcms::where('key', 'profile_fields')->firstOrCreate(['key' => 'profile_fields']);

if (isset($_POST ['save'])) {
    App::user()->languages = text::input_text(@$_POST['languages']);
    App::user()->lastname = text::for_name(@$_POST ['lastname']);
    App::user()->realname = text::for_name(@$_POST ['realname']);
    App::user()->middle_n = text::for_name(@$_POST ['middle_n']);

    if (isset($_POST ['ank_d_r'])) {
        if ($_POST ['ank_d_r'] == null)
            App::user()->ank_d_r = '';
        else {
            $ank_d_r = (int) $_POST ['ank_d_r'];
            if ($ank_d_r >= 1 && $ank_d_r <= 31)
                App::user()->ank_d_r = $ank_d_r;
            else
                $doc->err(__('Не корректный формат дня рождения'));
        }
    }

    if (isset($_POST ['ank_m_r'])) {
        if ($_POST ['ank_m_r'] == null)
            App::user()->ank_m_r = '';
        else {
            $ank_m_r = (int) $_POST ['ank_m_r'];
            if ($ank_m_r >= 1 && $ank_m_r <= 12)
                App::user()->ank_m_r = $ank_m_r;
            else
                $doc->err(__('Не корректный формат месяца рождения'));
        }
    }

    if (isset($_POST ['ank_g_r'])) {
        if ($_POST ['ank_g_r'] == null)
            App::user()->ank_g_r = '';
        else {
            $ank_g_r = (int) $_POST ['ank_g_r'];
            if ($ank_g_r >= date('Y') - 100 && $ank_g_r <= date('Y'))
                App::user()->ank_g_r = $ank_g_r;
            else
                $doc->err(__('Не корректный формат года рождения'));
        }
    }

    if (isset($_POST ['skype'])) {
        if (empty($_POST ['skype']))
            App::user()->skype = '';
        elseif (!is_valid::skype($_POST ['skype']))
            $doc->err(__('Указан не корректный %s', 'Skype login'));
        else {
            App::user()->skype = $_POST ['skype'];
        }
    }

    if (!empty($_POST ['wmid'])) {
        if (App::user()->wmid && App::user()->wmid != $_POST ['wmid']) {
            $doc->err(__('Активированный WMID изменять и удалять запрещено'));
        } elseif (!is_valid::wmid($_POST ['wmid'])) {
            $doc->err(__('Указан не корректный %s', 'WMID'));
        } elseif (App::user()->wmid != $_POST ['wmid']) {
            App::user()->wmid = $_POST ['wmid'];
        }
    }

    if (isset($_POST ['email'])) {
        if (empty($_POST ['email']))
            App::user()->email = '';
        elseif (!is_valid::mail($_POST ['email']))
            $doc->err(__('Указан не корректный %s', 'E-Mail'));
        else {
            App::user()->email = $_POST ['email'];
        }
    }

    App::user()->description = text::input_text(@$_POST ['description']);

    if (isset($_POST['profile_fields'])) {
        App::user()->info = $_POST['profile_fields'];
        App::user()->save();
        dd($_POST['profile_fields']);
    }
    $doc->msg(__('Параметры успешно приняты'));
}

$form = new form('?' . passgen());
foreach ($profileFields->options as $key => $title) {
    $form->text("profile_fields[{$key}]", __($title), App::user()->info{$key});
}
$form->text('lastname', __('Фамилия'), App::user()->lastname);
$form->text('realname', __('Имя'), App::user()->realname);
$form->text('middle_n', __('Отчество'), App::user()->middle_n);
$form->input('ank_d_r', __('Дата рождения'), App::user()->ank_d_r, 'text', false, 2, false, 2);
$form->input('ank_m_r', '', App::user()->ank_m_r, 'text',  false, 2, false, 2);
$form->input('ank_g_r', '', App::user()->ank_g_r, 'text',  true, 4, false, 4);
$form->text('skype', 'Skype', App::user()->skype);
$form->text('email', 'E-Mail', App::user()->email);
$form->text('wmid', 'WMID', App::user()->wmid);
$form->text('languages', __('Языки'), App::user()->languages ? App::user()->languages : $user_language_pack->name);
$form->textarea('description', __('О себе') . ' [512]', App::user()->description);

$form->button(__('Применить'), 'save');
$form->display();

$doc->ret(__('Личное меню'), '/menu.user.php');