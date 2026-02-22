@inject('schoolSettings','App\Classes\Settings\SchoolSettings')
<form action="{{ route('settings.school.update.school') }}" method="POST">
    @csrf
    @method('PATCH')
    <div class="row mt-3">
        <div class="col-md-8">
            <h4 class="text-primary border-bottom border-primary">{{ __('system.settings.school.day') }}</h4>
            <div class="school-setting mb-3">
                <label class="form-label">{{ __('system.settings.days') }}</label>
                <br/>
                @foreach(\App\Classes\Settings\Days::allOptions() as $dayId => $day)
                    <div class="form-check-inline">
                        <input
                                class="form-check-input @error('days') is-invalid @enderror"
                                type="checkbox"
                                value="{{ $dayId }}"
                                name="days[]"
                                id="day_{{ $dayId }}"
                                @checked(isset($schoolSettings->days[$dayId]))
                                aria-describedby="daysHelp"
                        />
                        <label class="form-check-label" for="day_{{ $dayId }}">{{ $day }}</label>
                    </div>
                @endforeach
                <x-utilities.error-display key="days">{{ $errors->first('days') }}</x-utilities.error-display>
                <div id="daysHelp" class="form-text">{{ __('system.settings.days.help') }}</div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="school-setting mb-3">
                        <label for="start_time">{{ __('system.settings.start') }}</label>
                        <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                               id="start_time" name="start_time"
                               value="{{ $schoolSettings->startTime }}" aria-describedby="startTimeHelp"/>
                        <x-utilities.error-display
                                key="start_time">{{ $errors->first('start_time') }}</x-utilities.error-display>
                        <div id="startTimeHelp" class="form-text">{{ __('system.settings.start.help') }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="school-setting mb-3">
                        <label for="end_time">{{ __('system.settings.end') }}</label>
                        <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time"
                               name="end_time"
                               value="{{ $schoolSettings->endTime }}" aria-describedby="endTimeHelp"/>
                        <x-utilities.error-display key="end_time">{{ $errors->first('end_time') }}</x-utilities.error-display>
                        <div id="endTimeHelp" class="form-text">{{ __('system.settings.end.help') }}</div>
                    </div>
                </div>
            </div>
            <h4 class="text-primary border-bottom border-primary">{{ __('system.settings.school.names') }}</h4>
            <div class="row">
                <div class="school-setting col-md">
                    <label for="student_name_format">{{ __('system.settings.names.student') }}</label>
                    <div class="input-group">
                        <input type="text" class="form-control"
                               value="{{ $schoolSettings->studentName->__toString() }}"
                               aria-describedby="studentNameHelp" readonly/>
                        <a class="btn btn-primary" href="{{ route('settings.school.name', $studentRole) }}"
                           role="button"><i class="fa-solid fa-edit"></i></a>
                    </div>
                    <div id="studentNameHelp"
                         class="form-text">{{ __('system.settings.names.students.help', ['sample' => $sampleStudent->name]) }}</div>
                </div>
                <div class="school-setting col-md">
                    <label for="employee_name_format">{{ __('system.settings.names.employee') }}</label>
                    <div class="input-group">
                        <input type="text" class="form-control"
                               value="{{ $schoolSettings->employeeName->__toString() }}"
                               aria-describedby="employeeNameHelp" readonly/>
                        <a class="btn btn-primary" href="{{ route('settings.school.name', $employeeRole) }}"
                           role="button"><i class="fa-solid fa-edit"></i></a>
                    </div>
                    <div id="employeeNameHelp"
                         class="form-text">{{ __('system.settings.names.employee.help', ['sample' => $sampleEmployee->name]) }}</div>
                </div>
                <div class="school-setting col-md">
                    <label for="parent_name_format">{{ __('system.settings.names.parent') }}</label>
                    <div class="input-group">
                        <input type="text" class="form-control"
                               value="{{ $schoolSettings->parentName->__toString() }}"
                               aria-describedby="parentNameHelp" readonly/>
                        <a class="btn btn-primary" href="{{ route('settings.school.name', $parentRole) }}"
                           role="button"><i class="fa-solid fa-edit"></i></a>
                    </div>
                    <div id="parentNameHelp"
                         class="form-text">{{ __('system.settings.names.parent.help', ['sample' => $sampleParent->name]) }}</div>
                </div>
            </div>
            <h4 class="text-primary border-bottom border-primary">{{ __('system.settings.school.ai') }}</h4>
            <livewire:settings.ai />
        </div>
        <div class="col-md-4">
            <h4 class="text-primary border-bottom border-primary">{{ __('system.settings.school.links') }}</h4>
            <div class="school-setting mb-3">
                <label for="terms_of_service">{{ __('system.settings.tos') }}</label>
                <input type="text" class="form-control @error('terms_of_service') is-invalid @enderror"
                       id="terms_of_service" name="terms_of_service"
                       value="{{ $schoolSettings->terms_of_service?: old('terms_of_service', '') }}"
                       aria-describedby="terms_of_serviceHelp"/>
                <x-utilities.error-display
                        key="terms_of_service">{{ $errors->first('terms_of_service') }}</x-utilities.error-display>
                <div id="terms_of_serviceHelp" class="form-text">{{ __('system.settings.tos.help') }}</div>
            </div>
            <div class="school-setting mb-3">
                <label for="privacy_policy">{{ __('system.settings.privacy') }}</label>
                <input type="text" class="form-control @error('privacy_policy') is-invalid @enderror"
                       id="privacy_policy" name="privacy_policy"
                       value="{{ $schoolSettings->privacy_policy?? old('privacy_policy', '') }}"
                       aria-describedby="privacy_policyHelp"/>
                <x-utilities.error-display
                        key="privacy_policy">{{ $errors->first('privacy_policy') }}</x-utilities.error-display>
                <div id="privacy_policyHelp" class="form-text">{{ __('system.settings.privacy.help') }}</div>
            </div>
            <livewire:storage.work-storage-browser
                    :fileable="$schoolSettings"
                    :title="__('system.settings.files')"
                    :show-links="true"
                    height="450px"
            />
        </div>
    </div>
    <div class="row mt-5">
        <button type="submit" class="btn btn-primary col">{{ __('system.settings.update') }}</button>
    </div>
</form>