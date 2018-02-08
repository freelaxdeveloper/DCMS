@extends('listing.listing')

@section('content')

    @each('news.list.news', $news, 'news', 'listing.empty')

@stop