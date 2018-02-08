<div class="pages">
    <?php echo App\pages::pages_helper(1, $page, $link); ?>

    <?php $__currentLoopData = $show_pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo App\pages::pages_helper($p, $page, $link); ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php echo App\pages::pages_helper($k_page, $page, $link); ?>

</div>