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
                            src="{{ $layout->getClassImage() }}"
                            alt="{{ __('integrators.local.classes.image') }}"
                    />
                    @if($editing)
                        <button
                            class="file btn btn-lg btn-dark"
                            wire:click="dispatch('document-storage-browser.open-browser',
                            {
                                config:
                                    {
                                        multiple: false,
                                        mimetypes: {{ Js::from(\App\Models\Utilities\MimeType::imageMimeTypes()) }},
                                        allowUpload: true,
                                        canSelectFolders: false,
                                        cb_instance: 'class-img'
                                    }
                            });"
                        >
                            {{ __('integrators.local.classes.image.update') }}
                        </button>
                    @endif
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
                                            src="{{ $student->person->portrait_url->thumbUrl() }}"
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
                                        <i class="fa-solid fa-comment"></i>
                                    </a>
                                    <a href="{{ route('people.show', $student->person->school_id) }}" class="text-primary fs-6">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <p>
                        {{ __('learning.criteria') }}
                        <a href="{{ route('learning.classes.criteria', $classSession->schoolClass) }}" class="ms-3 link-primary"><i class="fa-solid fa-edit"></i></a>
                    </p>
                    @else
                    <p>{{ __('learning.criteria') }}</p>
                    @endif
                    <table class="table table-sm table-borderless mb-3">
                        @php
                            $criteriaTotal = $classSession->classCriteria
                                ->reduce(fn(?int $carry, \App\Models\SubjectMatter\Learning\ClassCriteria $c) => $carry + $c->sessionCriteria->weight);
                        @endphp
                        @foreach($classSession->classCriteria as $criteria)
                            <tr>
                                <td>{{ $criteria->name }} ({{ $criteria->abbreviation }})</td>
                                <td>{{ round(($classSession->getCriteria($criteria)?->sessionCriteria->weight?? 0) / $criteriaTotal * 100, 2) }} %</td>
                            </tr>
                        @endforeach
                    </table>
                    @if(!$classSession->viewingAs(ClassViewer::FACULTY))
                        <p>{{ __('learning.demonstrations.assessment.summary') }}</p>
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
                                @foreach($classSession->teachers as $teacher)
                                    @if(!$loop->first), @endif
                                    <a href="{{ route('people.show', $teacher->school_id) }}">{{ $teacher->name }}</a>
                                @endforeach
                            </div>
                            <div>
                                {{ __('subjects.class.schedule') }}:
                                {{ $classSession->scheduleString() }}
                            </div>
                            <div>
                                {{ trans_choice('locations.rooms', 1) }}
                                @if($classSession->room)
                                    <a href="{{ route('locations.rooms.show', $classSession->room_id) }}">{{ $classSession->room->name }}</a>
                                @else
                                    {{ __('locations.rooms.no') }}
                                @endif
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
                                <a
                                        role="button"
                                        class="btn btn-info profile-edit-btn mt-2"
                                        href="{{ route('learning.classes.settings', $classSession) }}"
                                >{{ __('system.menu.classes.settings') }}</a>
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
