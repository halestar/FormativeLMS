@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <livewire:people.name-creator :role="$role"/>
    </div>
@endsection
