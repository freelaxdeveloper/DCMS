@extends('layouts.app')

@section('left_column')
    @parent()
    новый контент
@endsection

@section('content')
    {!! $content !!}
@endsection