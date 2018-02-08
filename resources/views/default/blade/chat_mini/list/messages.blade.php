@include('listing.posts', [
    'title' => $message->user->login,
    'icon' => $message->user->icon,
    'content' => $message->message, false,
    'time' => $message->created_at,
    'url' => route('chatminiActions', [$message->id]),
])