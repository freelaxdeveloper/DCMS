@extends('listing.listing')

@section('content')

    @include('listing.posts', [
        'title' => $user->group_name,
        'icon' => $user->icon,
    ])
    @include('listing.posts', [
        'title' => 'Дата рождения:',
        'content' => $user->ank_d_r . ' ' . \App\misc::getLocaleMonth($user->ank_m_r) . ' ' . $user->ank_g_r,
    ])
    @include('listing.posts', [
        'title' => 'Возраст:',
        'content' => \App\misc::get_age($user->ank_g_r, $user->ank_m_r, $user->ank_d_r, true),
    ])
    @include('listing.posts', [
        'title' => 'Баллы:',
        'counter' => $user->balls,
    ])
    @include('listing.posts', [
        'title' => 'О себе:',
        'content' => $user->description,
    ])

    @include('listing.posts', [
        'title' => 'Последний визит:',
        'content' => \App\misc::when($user->last_visit),
    ])

    @include('listing.posts', [
        'title' => 'Всего переходов:',
        'content' => $user->conversions,
    ])

    @include('listing.posts', [
        'title' => 'Дата регистрации:',
        'content' => date('d-m-Y', $user->reg_date),
    ])

@stop