<?php echo $__env->make('listing.posts', [
    'title' => $message->user->login,
    'icon' => $message->user->icon,
    'content' => $message->message, false,
    'time' => $message->created_at,
    'url' => route('chatminiActions', [$message->id]),
], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>