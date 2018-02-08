<div id="<?php echo e($id); ?>"
     class="post clearfix icon time <?php if($actions): ?>actions <?php endif; ?> <?php if($bottom): ?>bottom <?php endif; ?> <?php if($counter): ?>counter <?php endif; ?> <?php if($content): ?>content <?php endif; ?>"
     data-ng-controller="ListingPostCtrl"
     data-post-url="<?php echo e($url); ?>">
    <?php if($image): ?>
        <div class="post_image"><img src="<?= $image ?>" alt=""></div>
    <?php endif; ?>
    <div class="post_head">
        <span class="post_icon">
            <?php if(!empty($icon_class)): ?>
                <span class="<?php echo e($icon_class); ?>"></span>
            <?php elseif(!empty($icon)): ?>
                <img src="<?php echo e($icon); ?>" alt="">
            <?php endif; ?>
        </span>
        <a class="post_title" <?php if($url): ?> href="<?php echo e($url); ?>" <?php endif; ?>><?php echo $title; ?></a>
        <span class="post_actions">
            <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($action['url']); ?>"><img src="<?php echo e($action['icon']); ?>" alt="" /></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </span>
        <span class="post_counter"><?php echo e($counter); ?></span>
        <span class="post_time"><?php echo e($time); ?></span>
    </div>
    <div class="post_content"><?php echo $content; ?></div>
    <div class="post_bottom"><?php echo $bottom; ?></div>
</div>