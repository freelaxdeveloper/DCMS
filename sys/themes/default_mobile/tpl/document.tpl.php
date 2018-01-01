<?php
/**
 * @var document $this
 * @var user $user
 */
?><!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="stylesheet" href="/sys/themes/.common/system.css" type="text/css"/>
    <link rel="stylesheet" href="/sys/themes/.common/icons.css" type="text/css"/>
    <link rel="stylesheet" href="<?= $path ?>/res/style.css" type="text/css"/>
    <meta name="viewport" content="minimum-scale=1.0,initial-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta name="generator" content="DCMS <?= $dcms->version ?>"/>
    <? if ($description) { ?>
        <meta name="description" content="<?= $description ?>" /><? } ?>
    <? if ($keywords) { ?>
        <meta name="keywords" content="<?= $keywords ?>" /><? } ?>
    <script>
        window.translate = {
            'friends': "<?=__("Друзья")?>",
            'mail': "<?=__("Почта")?>",
            'user_menu': "<?= __("Личное меню") ?>",
            'auth': "<?= __("Авторизация") ?>",
            'reg': "<?= __("Регистрация") ?>",
            'rating_down_message': '<?=__('Подтвердите понижение рейтинга сообщения.').($dcms->forum_rating_down_balls?"\\n".__('Будет списано баллов: %s',$dcms->forum_rating_down_balls):'')?>'
        };

        window.user = <?=json_encode($user->getCustomData(array('id', 'group', 'friend_new_count', 'mail_new_count', 'login')))?>;
        window.URL = "<?=URL?>";
    </script>
    <script src="/sys/themes/.common/jquery-2.1.1.min.js"></script>
    <script src="/sys/themes/.common/dcmsApi.js"></script>
    <script src="<?= $path ?>/res/inputInsert.js"></script>
    <script src="<?= $path ?>/res/user.js"></script>
    <script src="<?= $path ?>/res/common.js"></script>
    <script src="<?= $path ?>/res/ajaxForm.js" async="async"></script>
    <script src="<?= $path ?>/res/smiles.js" async="async"></script>
    <script src="<?= $path ?>/res/listing.js" async="async"></script>
</head>
<body class="">
<audio id="audio_notify">
    <source src="/sys/themes/.common/notify.mp3"/>
    <source src="/sys/themes/.common/notify.ogg"/>
</audio>
<div id="container_content">
    <header id='title' class="<?= $returns ? 'returns' : '' ?>">
        <span class="tIcon menu"></span>
        <span class="tIcon home"></span>
        <span class="tIcon mail"></span>
        <span class="tIcon friend"></span>
        <span class="tIcon left">
            <ul>
                <?= $this->section($returns, '<li><span></span><a href="{url}">{name}</a></li>', true); ?>
            </ul>
        </span>
        <span class="t"><?= $title ?></span>
    </header>
    <? $this->displaySection('after_title') ?>
    <?php if ($tabs) { ?>
        <div id="tabs">
            <?= $this->section($tabs, '<a class="tab sel{selected}" href="{url}">{name}</a>', true); ?>
        </div>
    <?php } ?>
    <? $this->displaySection('before_content') ?>
    <section id="content">
        <div id="messages">
            <?= $this->section($err, '<div class="error">{text}</div>'); ?>
            <?= $this->section($msg, '<div class="info">{text}</div>'); ?>
        </div>
        <?php $this->displaySection('content') ?>
    </section>
    <? $this->displaySection('after_content') ?>
    <? $this->display('inc.foot.tpl') ?>
    <footer id="footer">
        <?= /** @var string $document_generation_time */
        __("Время генерации страницы: %s сек", $document_generation_time) ?><br/>
        <?= $copyright ?>
    </footer>
</div>
<aside id="menu_overflow">
    <div class="user">
        <span class="nick"><?= $user->nick ?></span>
        <a class="login" href="/login.php?return=<?= URL ?>"></a>
        <a class="reg" href="/reg.php?return=<?= URL ?>"></a>
    </div>
    <div class="menu">
        <? $this->displaySection('menu') ?>
    </div>
</aside>
</body>
</html>