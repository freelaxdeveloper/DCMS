<?php

include_once '../sys/inc/start.php';
use App\{document,is_valid,form,misc};
use App\App\App;

$doc = new document(1);
$doc->title = __('Мой логин');

$doc->ret(__('Личное меню'), '/menu.user.php');

$change_login = true ;
$error = '' ;
if(!$dcms->login_edit_time){
    $change_login = false ;
    $doc->err(__('Изменение логина запрещено')) ;
}
if($dcms->login_edit_time && $dcms->login_edit_time + App::user()->last_time_login > TIME){
    $change_login = false ;
    $error = __('Изменение будет доступно через: ' . misc::when($dcms->login_edit_time + App::user()->last_time_login)) ;
}
if($dcms->login_edit_balls && App::user()->balls < $dcms->login_edit_balls){
    $change_login = false ;    
}
if(isset($_POST['edit']) && $change_login && App::user()->login != $_POST['login']) {
    $login = $_POST['login'] ;
    if(is_valid::nick($_POST['login']) && $_POST['login'] == htmlspecialchars($_POST['login'])){
        $q = $db->prepare("INSERT INTO `login_history` (`id_user`, `time`, `login`) VALUES (?, ?, ?)") ;
        $q->execute(Array(App::user()->id, TIME, App::user()->login)) ;
        App::user()->login = $_POST['login'] ;
        App::user()->last_time_login = TIME ;
        App::user()->balls -= $dcms->login_edit_balls ;
        $doc->msg(__('Ваш логин успешно изменен')) ;
        header('Refresh: 1; ?') ;
        exit ;
    }else{
        $doc->err(__('Не корректный логин')) ;
    }
}
$form = new form('?' . passgen()) ;
$form->text('login', __('Логин'), App::user()->login, true, false, !$change_login ? true : false) ;
if($error){
    $form->bbcode('[notice] ' . __($error)) ;
}
if($dcms->login_edit_balls){
    $form->bbcode('[info] ' . __('Стоимость изменения') . ': ' . $dcms->login_edit_balls . misc::number($dcms->login_edit_balls, ' балл', ' балла', ' баллов')) ;
    if($dcms->login_edit_balls > App::user()->balls){
        $noBalls = $dcms->login_edit_balls - App::user()->balls ;
        $form->bbcode(__('[notice] Не достаточно: ' . ($noBalls) . misc::number($noBalls, ' балл', ' балла', ' баллов'))) ;
    }
}
$form->button(__('Изменить'), 'edit') ;
$form->display() ;