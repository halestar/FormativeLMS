<div class="container">
    <div class="row border-bottom mb-3">
        <h2 class="col-md-8">{{ trans_choice('learning.demonstrations', 2) }}</h2>
        <div class="col-md-2 align-self-center">
            <div class="input-group input-group-sm">
                <span class="input-group-text">{{ trans_choice('subjects.course', 1) }}:</span>
                <select class="form-select form-select-sm" wire:model="selectedCourseId" wire:change="setCourse">
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2 text-end align-self-center">
            <a
                href="{{ route('learning.ld.create', ['course' => $selectedCourseId]) }}"
                role="button"
                class="btn btn-primary btn-sm"
                wire:navigate
            >{{ trans('learning.demonstrations.new') }}</a>
        </div>
    </div>
    <div class="list-group">
        @foreach($demonstrations as $demonstration)
            <a href="{{ route('learning.ld.post', ['ld' => $demonstration->id]) }}"
               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
               wire:navigate
               wire:key="{{ $demonstration->id }}"
            >
                <h3>{{ $demonstration->name }} ({{ $demonstration->abbr }})</h3>
            </a>
        @endforeach
    </div>
</div>
