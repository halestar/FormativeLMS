@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-center">
            @if($person->isStudent() || $person->isParent())
                <x-homepage.student-classes></x-homepage.student-classes>
            @elseif($person->isTeacher())
                <x-homepage.faculty-classes :faculty="$person"></x-homepage.faculty-classes>
                <x-homepage.upcoming-sub-requests :faculty="$person"></x-homepage.upcoming-sub-requests>
            @endif
        </div>
    </div>
@endsection
