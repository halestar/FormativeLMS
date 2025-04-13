@extends('layouts.app', ['breadcrumb' => $breadcrumb])
@section('content')
    <div class="container">
        @livewire($type, $data)
    </div>
@endsection
