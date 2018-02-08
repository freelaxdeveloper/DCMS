@include('listing.posts', [
    'title' => $news->title,
    'icon' => 'news',
    'time' => $news->time,
    'content' => $news->text,
    'actions' => $news->actions,
    'url' => './comments.php?id=' . $news->id,
    'bottom' => "[url=/profile.view.php?id={$news->user->id}]{$news->user->login}[/url]",
])