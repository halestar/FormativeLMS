@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <livewire:locations.building-area-editor :area="$area" />
@endsection
