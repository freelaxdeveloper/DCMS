@include('listing.posts', [
    'title' => $theme->name,
    'bottom' => "Просмотров: {$theme->views_count}",
    'icon' => $theme->icon,
    'time' => $theme->time_last,
    'content' => $theme->lastUsers,
    'counter' => $theme->messages_count,
    'url' => "./theme.php?id={$theme->id}",
])