@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <livewire:people.role-fields-manager/>
    </div>
@endsection
