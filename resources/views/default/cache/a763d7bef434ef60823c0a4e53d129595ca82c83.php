<?php $__env->startSection('left_column'); ?>
    ##parent-placeholder-b253a569d91535d75adc742454ea08eaecaaf40e##
    новый контент
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $content; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>