@extends('layouts.class-settings', ['breadcrumb' => $breadcrumb, 'classSelected' => $classSelected])

@section('class_settings_content')
    <livewire:school.class-criteria-manager :school-class="$classSelected" />
@endsection