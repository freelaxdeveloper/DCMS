<?php echo $__env->make('listing.posts', [
    'title' => 'Гость',
    'icon' => 'guest',
    'content' => $user->info,
], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>