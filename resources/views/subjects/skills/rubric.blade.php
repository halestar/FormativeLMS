@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <livewire:assessment.rubric-builder :skill="$skill"/>
    </div>
@endsection
