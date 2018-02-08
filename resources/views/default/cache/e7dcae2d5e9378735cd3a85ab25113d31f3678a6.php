<?php echo $__env->make('listing.posts', [
    'title' => $category->name,
    'icon' => 'forum.category',
    'content' => $category->description,
    'url' => "./category.php?id={$category->id}",
], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>