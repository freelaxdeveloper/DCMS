@extends('listing.listing')

@section('content')
    @each('forum.list.themes', $themes, 'theme', 'listing.empty')
@stop