@include('listing.posts', [
    'title' => $comment->user->login,
    'icon' => $comment->user->icon,
    'time' => $comment->time,
    'content' => $comment->text,
    'actions' => $comment->actions,
    'url' => '/profile.view.php?id=' . $comment->user->id,
])