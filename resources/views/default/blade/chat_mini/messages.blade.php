@extends('listing.listing')

@section('content')
    @each('chat_mini.list.messages', $messages, 'message', 'listing.empty')
@stop