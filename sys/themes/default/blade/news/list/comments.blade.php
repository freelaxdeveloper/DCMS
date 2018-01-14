@include('listing.posts', [
    'title' => $comment->user->login,
    'icon' => $comment->user->icon,
    'time' => $comment->time,
    'content' => $comment->text,
    'actions' => [
        ['url' => "comment.delete.php?id={$comment->id}&amp;return=" . URL, 'icon' => 'delete'],
    ],
    'url' => '/profile.view.php?id=' . $comment->user->id,
])