@include('listing.posts', [
    'title' => $message->user->login,
    'icon' => $message->user->icon,
    'content' => $message->message,
    'time' => $message->time,
    'url' => "./actions.php?id={$message->id}",
])