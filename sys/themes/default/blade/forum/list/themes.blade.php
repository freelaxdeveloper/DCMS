@include('listing.posts', [
    'title' => $theme->name,
    'bottom' => "Просмотров: {$theme->countViews}",
    'icon' => $theme->icon,
    'time' => $theme->time_last,
    'content' => $theme->lastUsers,
    'counter' => $theme->countMessages,
    'url' => "./theme.php?id={$theme->id}",
])