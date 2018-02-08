<?php $__env->startSection('content'); ?>
    <?php echo $__env->renderEach('chat_mini.list.messages', $messages, 'message', 'listing.empty'); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('listing.listing', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>