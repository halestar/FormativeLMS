<div>
    <div class="d-flex align-content-stretch">
        <ul class="nav flex-column">
            <li
                    class="nav-item p-2 mb-1 border @if(!$classSession) border-end-0 @endif  rounded-start bg-danger-subtle"
                    wire:click="setTerm()"
            >
                <a
                        class="nav-link @if(!$classSession) active fw-bold @endif "
                        href="#"
                        @if(!$classSession) aria-current="page" @endif
                >{{ __('subjects.class.manager.terms.all') }}</a>
            </li>
            @foreach($schoolClass->year->campusTerms($schoolClass->course->campus)->get() as $term)
                @if($schoolClass->sessions()->where('term_id', $term->id)->exists())
                    <li
                            class="nav-item p-2 mb-1 border @if($classSession && $classSession->term_id == $term->id) border-end-0 @endif rounded-start"
                            wire:click="setTerm({{ $term->id }})"
                    >
                        <a
                                class="nav-link @if($classSession && $classSession->term_id == $term->id) active fw-bold @endif"
                                href="#"
                                @if($classSession && $classSession->term_id == $term->id) aria-current="page" @endif
                        >{{ $term->label }}</a>
                    </li>
                @else
                    <li class="nav-item p-2 mb-1 border rounded-start bg-secondary">
                        <button
                                type="button"
                                class="btn btn-primary"
                                wire:click="createClassSession({{ $term->id }})"
                        >{{ __('subjects.class.manager.terms.add', ['term' => $term->label]) }}</button>
                    </li>
                @endif
            @endforeach
            <li class="flex-grow-1 border-end"></li>
        </ul>
        <div class="border border-start-0 rounded-end p-3 flex-grow-1">
            @if(!$classSession)
                <div class="border-bottom bg-danger-subtle p-3 mt-n3 mx-n3 mb-1 fw-bold text-center fs-5">
                    {{ __('subjects.class.manager.terms.all.warning') }}
                </div>
            @endif
            <h2 class="border-bottom pb-2 mb-3 d-flex justify-content-between align-items-center">
                {{ $schoolClass->name }}

            </h2>
            <div class="row">
                <div class="col-md-6">
                    <h3 class="border-bottom pb-2 mb-3">{{ __('subjects.class.schedule') }}</h3>
                    <div class="mb-3">
                        <label
                                class="form-label fs-6 fw-bold me-3">{{ __('subjects.class.manager.schedule.type') }}</label>
                        <div class="form-check form-check-inline">
                            <input
                                    class="form-check-input"
                                    type="radio"
                                    id="schedule_block"
                                    name="schedule_type"
                                    value="block"
                                    wire:model.live="scheduleType"
                            />
                            <label class="form-check-label"
                                   for="schedule_block">{{ trans_choice('locations.block',1) }}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input
                                    class="form-check-input"
                                    type="radio"
                                    id="schedule_periods"
                                    name="schedule_type"
                                    value="periods"
                                    wire:model.live="scheduleType"
                            />
                            <label class="form-check-label"
                                   for="schedule_periods">{{ trans_choice('locations.period',2) }}</label>
                        </div>
                    </div>
                    @if($scheduleType == "block")
                        <div class="mb-3">
                            <label for="block_id"
                                   class="form-label">{{ trans_choice('locations.block',1)  }}</label>
                            <select id="block_id" class="form-select" wire:model="block_id">
                                <option>{{ __('subjects.class.manager.block.select') }}</option>
                                @foreach($campus->blocks as $block)
                                    <option value="{{ $block->id }}">{{ $block->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="room_id" class="form-label">{{ trans_choice('locations.rooms',1)  }}</label>
                            <select id="room_id" class="form-select" wire:model="room_id">
                                <option>{{ __('subjects.class.manager.room.select') }}</option>
                                @foreach($campus->rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="d-flex mb-3 justify-content-start">
                            @foreach(\App\Classes\Days::weekdaysOptions() as $dayId => $dayName)
                                <div class="mx-2 text-center">
                                    <label for="day-select-{{ $dayId }}"
                                           class="form-label fw-bold text-decoration-underline">{{ $dayName }}</label>
                                    <select
                                            class="form-select @error('periods') is-invalid @enderror"
                                            id="day-select-{{ $dayId }}"
                                            name="periods[]"
                                            multiple
                                            wire:model.live="periods.{{ $dayId }}"
                                            size="{{ $campus->periods($dayId)->active()->count() }}"
                                    >
                                        @foreach($campus->periods($dayId)->active()->get() as $period)
                                            <option
                                                    value="{{ $period->id }}"
                                            >{{ $period->abbr }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input
                                        class="form-check-input"
                                        type="radio"
                                        id="room_single"
                                        name="room_type"
                                        value="single"
                                        wire:model.live="room_type"
                                        @if($room_type == "single") checked @endif
                                />
                                <label class="form-check-label"
                                       for="room_single">{{ __('subjects.class.manager.room.single') }}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input
                                        class="form-check-input"
                                        type="radio"
                                        id="room_multiple"
                                        name="room_type"
                                        value="multiple"
                                        wire:model.live="room_type"
                                        @if($room_type == "multiple") checked @endif
                                />
                                <label class="form-check-label"
                                       for="room_multiple">{{ __('subjects.class.manager.room.multiple') }}</label>
                            </div>
                        </div>
                        @if($room_type == "single")
                            <div class="mb-3">
                                <label for="room_id"
                                       class="form-label">{{ trans_choice('locations.rooms',1)  }}</label>
                                <select id="room_id" class="form-select" wire:model="room_id">
                                    <option>{{ __('subjects.class.manager.room.select') }}</option>
                                    @foreach($campus->rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            @foreach(\App\Classes\Days::getWeekdays() as $dayId)
                                @foreach($periods[$dayId] as $period)
                                    <div class="input-group mb-3" wire:key="{{ $period }}">
                                        <label for="room-period-{{ $period }}"
                                               class="input-group-text">{{ \App\Models\Schedules\Period::find($period)->name }}</label>
                                        <select class="form-select" wire:model="multiple_rooms.{{ $period }}">
                                            <option>{{ __('locations.rooms.no') }}</option>
                                            @foreach($campus->rooms as $room)
                                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                @endforeach
                            @endforeach
                        @endif
                    @endif
                </div>
                <div class="col-md-6">
                    <h3 class="border-bottom pb-2 mb-3">{{ trans_choice('subjects.class.teacher', $teachers->count()) }}</h3>

                    <ul class="list-group my-3">
                        @foreach($teachers as $teacher)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <img src="{{ $teacher->thumbnail_url }}" class="img-thumbnail img-fluid"/>
                                    <span class="fs-4">{{ $teacher->name }}</span>
                                </span>
                                <button
                                        type="button"
                                        class="btn btn-danger"
                                        wire:click="removeTeacher({{ $teacher->id }})"
                                ><i class="fa-solid fa-times"></i></button>
                            </li>
                        @endforeach
                    </ul>

                    <div class="form-floating mb-0">
                        <input
                                type="text"
                                class="form-control"
                                id="search_teacher"
                                placeholder="{{ __('subjects.class.manager.teacher.search') }}"
                                autocomplete="off"
                                wire:model.live.debounce="search_teacher"
                        />
                        <label for="search_teacher">{{ __('subjects.class.manager.teacher.search') }}</label>
                    </div>
                    @if($suggestedTeachers && $suggestedTeachers->count() > 0)
                        <div class="absolute m-0 rounded w-full bg-gray-200 pl-2">
                            <ul class="list-group">
                                @foreach($suggestedTeachers as $suggestion)
                                    <li
                                            class="list-group-item list-group-item-action"
                                            wire:key="{{ $suggestion->id }}"
                                            wire:click="addTeacher({{ $suggestion->id }})"
                                    >
                                        <div class="row">
                                            <div class="col-2">
                                                <img src="{{ $suggestion->thumbnail_url }}"
                                                     class="img-thumbnail img-fluid"/>
                                            </div>
                                            <div class="col-6">
                                                {{ $suggestion->name }}
                                            </div>
                                            <div class="col-4 text-end">
                                                {{ $suggestion->employeeCampuses->pluck('abbr')->join(', ') }}
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
            <div class="row">
                <button
                        type="button"
                        class="col btn btn-primary mx-2"
                        wire:click="save()"
                >{{ trans_choice('subjects.class.update', 1) }}</button>
                <a class="col btn btn-secondary mx-2"
                   href="{{ route('subjects.classes.index', ['course' => $schoolClass->course_id]) }}">{{ __('common.cancel') }}</a>
            </div>
        </div>
    </div>
</div>
