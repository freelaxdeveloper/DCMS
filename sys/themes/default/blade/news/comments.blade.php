@extends('listing.listing')

@section('content')

    @include('listing.posts', [
        'title' => $news->title,
        'icon' => 'news',
        'time' => $news->time,
        'content' => $news->text,
        'actions' => $actions,
        'bottom' => "[url=/profile.view.php?id={$news->id_user}]{$news->user->login}[/url]",
    ])

    {!! $form !!}

    @each('news.list.comments', $comments, 'comment', 'listing.empty')
@stop