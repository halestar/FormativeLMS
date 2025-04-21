@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <livewire:assessment.skill-category-browser />
    </div>
@endsection
