<div>
    I need you to develop a rubric for the
    Knowledge skill with designation (or name) of "{{ $skill->name?? $skill->designation }}". It is meant to asses
    students in
    the {{ $skill->levels->pluck('name')->join(', ') }} grades and it will be assessing the {{ $skill->subject->name }}
    subject. The description is as follows:

    {!! $skill->description !!}

</div>