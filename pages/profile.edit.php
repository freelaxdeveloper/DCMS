<?php

include_once '../sys/inc/start.php';
$doc = new document(1);
$doc->title = __('Мой профиль');

if (isset($_POST ['save'])) {
    $user->languages = text::input_text(@$_POST['languages']);
    $user->lastname = text::for_name(@$_POST ['lastname']);
    $user->realname = text::for_name(@$_POST ['realname']);
    $user->middle_n = text::for_name(@$_POST ['middle_n']);
    $user->icq_uin = text::icq_uin(@$_POST ['icq']);

    if (isset($_POST ['ank_d_r'])) {
        if ($_POST ['ank_d_r'] == null)
            $user->ank_d_r = '';
        else {
            $ank_d_r = (int) $_POST ['ank_d_r'];
            if ($ank_d_r >= 1 && $ank_d_r <= 31)
                $user->ank_d_r = $ank_d_r;
            else
                $doc->err(__('Не корректный формат дня рождения'));
        }
    }

    if (isset($_POST ['ank_m_r'])) {
        if ($_POST ['ank_m_r'] == null)
            $user->ank_m_r = '';
        else {
            $ank_m_r = (int) $_POST ['ank_m_r'];
            if ($ank_m_r >= 1 && $ank_m_r <= 12)
                $user->ank_m_r = $ank_m_r;
            else
                $doc->err(__('Не корректный формат месяца рождения'));
        }
    }

    if (isset($_POST ['ank_g_r'])) {
        if ($_POST ['ank_g_r'] == null)
            $user->ank_g_r = '';
        else {
            $ank_g_r = (int) $_POST ['ank_g_r'];
            if ($ank_g_r >= date('Y') - 100 && $ank_g_r <= date('Y'))
                $user->ank_g_r = $ank_g_r;
            else
                $doc->err(__('Не корректный формат года рождения'));
        }
    }

    if (isset($_POST ['skype'])) {
        if (empty($_POST ['skype']))
            $user->skype = '';
        elseif (!is_valid::skype($_POST ['skype']))
            $doc->err(__('Указан не корректный %s', 'Skype login'));
        else {
            $user->skype = $_POST ['skype'];
        }
    }

    if (!empty($_POST ['wmid'])) {
        if ($user->wmid && $user->wmid != $_POST ['wmid']) {
            $doc->err(__('Активированный WMID изменять и удалять запрещено'));
        } elseif (!is_valid::wmid($_POST ['wmid'])) {
            $doc->err(__('Указан не корректный %s', 'WMID'));
        } elseif ($user->wmid != $_POST ['wmid']) {
            $user->wmid = $_POST ['wmid'];
        }
    }

    if (isset($_POST ['email'])) {
        if (empty($_POST ['email']))
            $user->email = '';
        elseif (!is_valid::mail($_POST ['email']))
            $doc->err(__('Указан не корректный %s', 'E-Mail'));
        else {
            $user->email = $_POST ['email'];
        }
    }

    $user->description = text::input_text(@$_POST ['description']);

    $doc->msg(__('Параметры успешно приняты'));
}

$form = new form('?' . passgen());
$form->text('lastname', __('Фамилия'), $user->lastname);
$form->text('realname', __('Имя'), $user->realname);
$form->text('middle_n', __('Отчество'), $user->middle_n);
$form->input('ank_d_r', __('Дата рождения'), $user->ank_d_r, 'text', false, 2, false, 2);
$form->input('ank_m_r', '', $user->ank_m_r, 'text',  false, 2, false, 2);
$form->input('ank_g_r', '', $user->ank_g_r, 'text',  true, 4, false, 4);
$form->text('icq', 'ICQ UIN', $user->icq_uin);
$form->text('skype', 'Skype', $user->skype);
$form->text('email', 'E-Mail', $user->email);
$form->text('wmid', 'WMID', $user->wmid);
$form->text('languages', __('Языки'), $user->languages ? $user->languages : $user_language_pack->name);
$form->textarea('description', __('О себе') . ' [512]', $user->description);

$form->button(__('Применить'), 'save');
$form->display();

$doc->ret(__('Личное меню'), '/menu.user.php');