@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-center">
            @if($person->isStudent())
                <x-homepage.student-classes :student="$person->student()"></x-homepage.student-classes>
            @elseif($person->isTeacher())
                <x-homepage.faculty-classes :faculty="$person"></x-homepage.faculty-classes>
            @endif
        </div>
    </div>
@endsection
