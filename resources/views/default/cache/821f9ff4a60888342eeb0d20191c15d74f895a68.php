<?php $__env->startSection('content'); ?>

    <?php echo $__env->make('listing.posts', [
        'title' => $user->group_name,
        'icon' => $user->icon,
    ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php echo $__env->make('listing.posts', [
        'title' => 'Дата рождения:',
        'content' => $user->ank_d_r . ' ' . \App\misc::getLocaleMonth($user->ank_m_r) . ' ' . $user->ank_g_r,
    ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php echo $__env->make('listing.posts', [
        'title' => 'Возраст:',
        'content' => \App\misc::get_age($user->ank_g_r, $user->ank_m_r, $user->ank_d_r, true),
    ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php echo $__env->make('listing.posts', [
        'title' => 'Баллы:',
        'counter' => $user->balls,
    ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php echo $__env->make('listing.posts', [
        'title' => 'О себе:',
        'content' => $user->description,
    ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo $__env->make('listing.posts', [
        'title' => 'Последний визит:',
        'content' => \App\misc::when($user->last_visit),
    ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo $__env->make('listing.posts', [
        'title' => 'Всего переходов:',
        'content' => $user->conversions,
    ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo $__env->make('listing.posts', [
        'title' => 'Дата регистрации:',
        'content' => date('d-m-Y', $user->reg_date),
    ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('listing.listing', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>