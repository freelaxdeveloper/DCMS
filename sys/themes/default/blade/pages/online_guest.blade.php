@extends('listing.listing')

@section('content')
    @each('pages.list.guests', $guests, 'user', 'listing.empty')
@stop