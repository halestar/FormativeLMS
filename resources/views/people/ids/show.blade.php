@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <x-people.id-viewer/>
        </div>
    </div>
@endsection
