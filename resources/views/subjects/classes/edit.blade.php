@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <livewire:subject-matter.school-class-manager :schoolClass="$schoolClass"/>
    </div>
@endsection
