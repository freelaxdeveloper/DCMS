@include('listing.posts', [
    'title' => 'Гость',
    'icon' => 'guest',
    'content' => $user->info,
])