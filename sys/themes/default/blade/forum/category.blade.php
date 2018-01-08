@extends('listing.listing')

@section('content')
    @each('forum.list.categories', $categories, 'category', 'listing.empty')
@stop