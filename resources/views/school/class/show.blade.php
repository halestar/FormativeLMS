@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    @livewire(config('class_management.component'), ['classSession' => $classSession])
@endsection
