@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
<div class="container">
    <livewire:locations.mass-create-periods :campus="$campus" />
</div>
@endsection
