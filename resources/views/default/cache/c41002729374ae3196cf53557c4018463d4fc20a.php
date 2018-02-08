<!DOCTYPE html>
<html lang="en" ng-app="Dcms">
<head>
    <link rel="shortcut icon" href="/favicon.ico"/>

    <link rel="stylesheet" href="<?php echo e(elixir('default/css/core.css')); ?>" type="text/css"/>

    <script charset="utf-8" src="<?php echo e(elixir('default/js/core.js')); ?>" type="text/javascript"></script>
    <script charset="utf-8" src="<?php echo e(elixir('default/js/highcharts.js')); ?>" type="text/javascript"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $__env->yieldContent('title', $title); ?></title>

    <script>
        user = <?php echo $current_user; ?>;
        translates = {
            bbcode_b: <?= __('\'Текст жирным шрифтом\'') ?>,
            bbcode_i: <?= __('\'Текст курсивом\'') ?>,
            bbcode_u: <?= __('\'Подчеркнутый текст\'') ?>,
            bbcode_img: <?= __('\'Вставка изображения\'') ?>,
            bbcode_php: <?= __('\'Выделение PHP-кода\'') ?>,
            bbcode_big: <?= __('\'Увеличенный размер шрифта\'') ?>,
            bbcode_small: <?= __('\'Уменьшенный размер шрифта\'') ?>,
            bbcode_gradient: <?= __('\'Цветовой градиент\'') ?>,
            bbcode_hide: <?= __('\'Скрытый текст\'') ?>,
            bbcode_spoiler: <?= __('\'Свернутый текст\'') ?>,
            smiles: <?= __('\'Смайлы\'') ?>,
            form_submit_error: <?= __('\'Ошибка связи...\'') ?>,
            auth: <?= __('\'Авторизация\'') ?>,
            reg: <?= __('\'Регистрация\'') ?>,
            friends: <?= __('\'Друзья\'') ?>,
            mail: <?= __('\'Почта\'') ?>,
            error: <?= __('\'Неизвестная ошибка\'') ?>,
            rating_down_message: <?= __('\'Подтвердите понижение рейтинга сообщения.\nБудет списано баллов: 50\'') ?>
        };
        codes = [
            {Text: 'B', Title: translates.bbcode_b, Prepend: '[b]', Append: '[/b]'},
            {Text: 'I', Title: translates.bbcode_i, Prepend: '[i]', Append: '[/i]'},
            {Text: 'U', Title: translates.bbcode_u, Prepend: '[u]', Append: '[/u]'},
            {Text: 'BIG', Title: translates.bbcode_big, Prepend: '[big]', Append: '[/big]'},
            {Text: 'Small', Title: translates.bbcode_small, Prepend: '[small]', Append: '[/small]'},
            {Text: 'IMG', Title: translates.bbcode_img, Prepend: '[img]', Append: '[/img]'},
            {Text: 'PHP', Title: translates.bbcode_php, Prepend: '[php]', Append: '[/php]'},
            {Text: 'SPOILER', Title: translates.bbcode_spoiler, Prepend: '[spoiler title=""]', Append: '[/spoiler]'},
            {Text: 'HIDE', Title: translates.bbcode_hide, Prepend: '[hide group="1" balls="1"]', Append: '[/hide]'}
        ];
    </script>
    <style type="text/css">
        .ng-hide {
            display: none !important;
        }
    </style>
</head>
<body class="theme_light_full theme_light" ng-controller="DcmsCtrl">
    <div id="main">
        <div id="top_part">
            <div id="header">
                <div class="body_width_limit clearfix">
                    <h1 id="title"><?php echo e($title); ?></h1>

                    <div id="navigation" class="clearfix <?php if( $is_main ): ?> ng-hide <?php endif; ?>">
                        <a class="nav_item" href='/'><?= __('Главная') ?></a>
                         <?php $__currentLoopData = $returns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a class="nav_item" href="<?php echo e($link->url); ?>"><?php echo e($link->name); ?></a>
                         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <span class="nav_item"><?php echo e($title); ?></span>
                    </div>
                    <div id="tabs" class=" !$tabs ? 'ng-hide' : '' ?>">
                        <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a class="tab sel<?php echo e($link->selected); ?>" href="<?php echo e($link->url); ?>"><?php echo e($link->name); ?></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <div id="navigation_user">
                    <div class="body_width_limit clearfix">
                        <a ng-show="+user.group" class="<?php if( !$user->group ): ?>ng-hide <?php endif; ?>"
                           href="<?php echo e(route('user:menu')); ?>" ng-bind="user.login"><?php echo e($user->login); ?></a>
                        <a ng-show="+user.friend_new_count" class='ng-hide'
                           href='/my.friends.php' ng-bind="str.friends"><?= __('Друзья') ?></a>
                        <a ng-show="+user.mail_new_count" class='ng-hide'
                           href='/my.mail.php?only_unreaded' ng-bind="str.mail"><?= __('Почта') ?></a>
                        <a ng-hide="+user.group" class="ng-hide"
                           href="<?php echo e(route('auth:login')); ?>" ng-bind="translates.auth"><?= __('Авторизаци') ?></a>
                        <a ng-hide="+user.group" class="ng-hide"
                           href="<?php echo e(route('auth:register')); ?>" ng-bind="translates.reg"><?= __('Регистрация') ?></a>
    
                        <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                           <a class="action" href="<?php echo e($link->url); ?>"><?php echo e($link->name); ?></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                 <?php echo e($header); ?>

            </div>
            <div class="body_width_limit clearfix">
                <div id="left_column">
                    <?php $__env->startSection('left_column'); ?>
                        <?php echo $left_column; ?>

                    <?php echo $__env->yieldSection(); ?>
                </div>
                <div id="content">
                    <div id="messages">
                         <?php $__currentLoopData = $errors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="error"><?php echo e($error->text); ?></div>
                         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                         <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="info"><?php echo e($message->text); ?></div>
                         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </div>
            <div id="empty"></div>
        </div>
        <div id="footer">
            <div class="body_width_limit">
                        <span id="copyright">
                             <?= App\text::toOutput($dcms->copyright) ?>
                        </span>
                        <span id="language">
                             <?= __('Язык') ?>:<a href='/language.php?return=<?php echo e(URL); ?>'
                                                 style='background-image: url(<?php echo e($lang->icon); ?>); background-repeat: no-repeat; background-position: 5px 2px; padding-left: 23px;'> <?php echo e($lang->name); ?></a>
                        </span>
                        <span id="generation">
                             <?= __('Время генерации страницы: %s сек', $document_generation_time) ?>
                        </span>
            </div>
        </div>
    </div>
</body>
</html>