@extends('layouts.class-settings', ['breadcrumb' => $breadcrumb, 'classSelected' => $classSelected])

@section('class_settings_content')
    <livewire:school.class-management-settings :school-class="$classSelected" />
@endsection