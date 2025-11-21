@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <livewire:school.class-management.class-page :classSession="$classSession" :person="$person" :viewRole="$viewRole">
@endsection
