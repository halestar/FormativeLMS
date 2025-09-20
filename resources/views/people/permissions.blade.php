@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <livewire:people.field-permissions-editor/>
    </div>
@endsection
