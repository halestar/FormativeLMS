@extends(backpack_view('blank'))

@php
    $widgets['before_content'][] = [
        'type'        => 'jumbotron',
        'heading'     => __('system.menu.crud'),
        'button_link' => "/home",
        'button_text' => __('system.menu.home'),
        'heading_class' => 'display-3 text-white',
    ];
@endphp

@section('content')
@endsection
