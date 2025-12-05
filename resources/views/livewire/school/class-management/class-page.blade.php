@use('App\Enums\ClassViewer')
<div class="container">
    <div class="row">
        {{-- Profile Image and Settings Column --}}
        <div class="col-md-4">
            <div class="d-flex flex-column">
                {{-- Profile Image --}}
                <div class="profile-img">
                    <img
                            class="img-fluid img-thumbnail"
                            src="{{ $classSession->course->campus->img }}"
                            alt="{{ __('locations.campus.img') }}"
                    />
                </div>
                {{-- Personal Settings and Links --}}
                <div class="profile-work w-100">
                    @if($classSession->viewingAs(ClassViewer::FACULTY))
                    <p>{{ __('school.student.roster') }}</p>
                    <ul class="list-group my-3 list-group-flush">
                        @foreach($classSession->students as $student)
                            @continue($classSession->viewingAs(ClassViewer::ADMIN) && !$self->isTrackingStudent($student))
                            <li class="list-group-item list-group-item-action p-0 py-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <img
                                            class="img-fluid me-2 avatar-img-normal rounded-circle"
                                            src="{{ $student->person->thumbnail_url }}"
                                            alt="{{ $student->person->name }}"
                                    />
                                    {{ $student->person->name }}
                                </div>
                                <div class="text-end">
                                    <a
                                        href="#"
                                        class="text-info fs-6 me-2"
                                        wire:click="dispatch('class-messages-change-conversation', { session: '{{ $classSession->id }}', student: '{{ $student->id }}' })"
                                    >
                                        <i class="fa-solid fa-envelope"></i>
                                    </a>
                                    <a href="{{ route('people.show', $student->person->school_id) }}" class="text-primary fs-6">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="d-grid gap-2">
                        <a class="btn btn-primary" role="button" href="{{ route('learning.criteria', ['classSession' => $classSession->id] ) }}">
                            {{ __('learning.criteria') }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        {{-- Main Content Column --}}
        <div class="col-md-8">
            <div class="row mb-3">
                {{-- Basic Info --}}
                <div class="col-md-8">
                    <div class="profile-head d-flex align-items-start flex-column h-100">
                        <h5>
                            {{ $classSession->name }}
                        </h5>
                        <h6 class="d-flex flex-column w-100">
                            <div>
                                {{ trans_choice('subjects.class.teacher', $classSession->teachers()->count()) }}:
                                {{ $classSession->teachersString() }}
                            </div>
                            <div>
                                {{ __('subjects.class.schedule') }}:
                                {{ $classSession->scheduleString() }}
                            </div>
                        </h6>
                    </div>
                </div>
                {{-- User Control --}}
                <div class="col-md-4">
                    <div class="d-flex flex-column align-items-center">
                        @can('manage', $classSession)
                            @if($editing)
                                <button
                                        type="button"
                                        class="btn btn-danger profile-edit-btn"
                                        wire:click="dispatch('class-page-set-editing', {editing: false})"
                                >{{ __('people.profile.editing') }}</button>
                            @else
                                <button
                                        type="button"
                                        class="btn btn-secondary profile-edit-btn"
                                        wire:click="dispatch('class-page-set-editing', {editing: true})"
                                >{{ __('subjects.class.edit') }}</button>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
            <livewire:school.class-management.top-announcement :session="$classSession" classes="mb-3" />

            {{-- Profile Tabs --}}
            <livewire:school.class-management.class-tabs :session="$classSession" />

        </div>
    </div>
</div>
@script
<script>
    $wire.on('close-add-tabs', () => {
        $('#class-modal').modal('hide')
    });
</script>
@endscript
