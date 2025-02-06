<div class="card">
    <div class="card-header">{{ __('subjects.class.mine') }}</div>
        <div class="list-group list-group-flush">
            @foreach ($classes as $class)
                <a
                    href="{{ route('subjects.school.classes.show', $class) }}"
                    class="list-group-item list-group-item-action"
                    style="background-color: {{ $class->course->subject->color }}; color: {{ $class->course->subject->getTextHex() }};"
                >
                    <div class="row">
                        <div class="col-8 marquee-text">
                            <span>
                                {{ $class->name }}
                                [ {{ $class->teachersString() }} ]
                                {{ $class->scheduleString() }}
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
    </div>
</div>
