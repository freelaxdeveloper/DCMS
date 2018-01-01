<?php
/**
 * @var $this document
 */
?><!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?= $lang->xml_lang ?>">
<head>
    <title><?= $title ?></title>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="stylesheet" href="/sys/themes/.common/system.css" type="text/css"/>
    <link rel="stylesheet" href="/sys/themes/.common/theme_light.css" type="text/css"/>
    <link rel="stylesheet" href="<?= $path ?>/style.css" type="text/css"/>
    <meta http-equiv="content-Type" content="application/xhtml+xml; charset=utf-8"/>
    <meta name="generator" content="DCMS <?= $dcms->version ?>"/>
    <? if ($description) { ?>
        <meta name="description" content="<?= $description ?>" /><? } ?>
    <? if ($keywords) { ?>
        <meta name="keywords" content="<?= $keywords ?>" /><? } ?>
    <style>
        .hide {
            display: none !important;
        }
    </style>
</head>
<body class="theme_light theme_light_light">
<div>
    <? $this->display('inc.title.tpl') ?>
    <? $this->displaySection('after_title')?>
    <div id="tabs">
        <?= $this->section($tabs, '<a class="tab sel{selected}" href="{url}">{name}</a>', true); ?>
    </div>
    <? $this->display('inc.user.tpl') ?>
    <? $this->displaySection('before_content')?>
    <div id="content">
        <div id="messages">
            <?= $this->section($err, '<div class="err">{text}</div>'); ?>
            <?= $this->section($msg, '<div class="msg">{text}</div>'); ?>
        </div>
        <?php $this->displaySection('content') ?>
    </div>
    <? $this->displaySection('after_content')?>
    <? $this->display('inc.foot.tpl') ?>
    <div id="foot">
        <?= __("Время генерации страницы: %s сек", $document_generation_time) ?><br/>
        <?= $copyright ?>
    </div>
</div>
</body>
</html>