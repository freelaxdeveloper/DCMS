<?php $__env->startSection('content'); ?>

    <?php echo $__env->renderEach('news.list.news', $news, 'news', 'listing.empty'); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('listing.listing', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>