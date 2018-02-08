<div class="post clearfix icon <?php if(!empty($actions)): ?>actions <?php endif; ?> <?php if(!empty($bottom)): ?>bottom <?php endif; ?> <?php if(!empty($time)): ?>time <?php endif; ?> <?php if(!empty($content)): ?>content <?php endif; ?> <?php if(isset($counter)): ?>counter <?php endif; ?>"
    data-ng-controller="ListingPostCtrl"
    data-post-url="<?php if(!empty($url)): ?><?php echo e($url); ?><?php endif; ?>">
    <div class="post_head">
        <?php if(!empty($icon)): ?>
            <span class="post_icon">
                <img src="<?php echo e(App\App\App::icon($icon)); ?>" alt="">
            </span>
        <?php endif; ?>
        <?php if(empty($url)): ?>
            <?php echo e($title); ?>

        <?php else: ?>
            <a class="post_title" href="<?php echo e($url); ?>"><?php echo e($title); ?></a>
        <?php endif; ?>
        <span class="post_actions">
            <?php if(!empty($actions)): ?>
                <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($action['url']); ?>"><img src="<?php echo e(App\App\App::icon($action['icon'])); ?>" alt="" /></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </span>
        <?php if(!empty($counter)): ?>
            <span class="post_counter"><?php echo e($counter); ?></span>
        <?php endif; ?>
        <?php if(!empty($time)): ?>
            <span class="post_time"><?php echo e($time); ?></span>
        <?php endif; ?>
    </div>
    <?php if(!empty($content)): ?>
        <div class="post_content"><?= App\text::toOutput($content) ?></div>
    <?php endif; ?>
    <?php if(!empty($bottom)): ?>
        <div class="post_bottom"><?= App\text::toOutput($bottom) ?></div>
    <?php endif; ?>
</div>
