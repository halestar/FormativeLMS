@extends('layouts.class-settings', ['breadcrumb' => $breadcrumb, 'classSelected' => $classSelected])

@section('class_settings_content')
    <livewire:school.class-management-settings
            :school-class="$classSelected"
            style="background-color: {{ $classSelected->subject->color }}; color: {{ $classSelected->subject->getTextHex() }} ;border-color: {{ $classSelected->subject->color }};"
    />
@endsection