@extends('layouts.app', ['breadcrumb' => [$classSession->name_with_schedule => '#']])

@section('content')
    <div class="container">
        <div class="alert alert-danger">
            {!! __('school.classes.management.setup.no') !!}
        </div>
    </div>
@endsection