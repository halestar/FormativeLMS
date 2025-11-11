@inject('schoolSettings','App\Classes\Settings\SchoolSettings')
<form action="{{ route('settings.school.update.classes') }}" method="POST">
    @csrf
    @method('PATCH')
    <div class="school-setting mb-3">
        <label for="max_msg">{{ __('system.settings.classes.max_msg') }}</label>
        <input
                type="number"
                class="form-control @error('max_msg') is-invalid @enderror"
                id="max_msg"
                name="max_msg"
                value="{{ $schoolSettings->max_msg }}"
                aria-describedby="maxMsgHelp"/>
        <x-utilities.error-display key="max_msg">{{ $errors->first('max_msg') }}</x-utilities.error-display>
        <div id="maxMsgHelp" class="form-text">{{ __('system.settings.classes.max_msg.help') }}</div>
    </div>

    <div class="school-setting mb-3">
        <label class="form-label">{{ __('system.settings.classes.year_messages') }}</label>
        <br/>
        <div class="form-check-inline">
            <input
                    class="form-check-input @error('days') is-invalid @enderror"
                    type="radio"
                    value="{{ \App\Classes\Settings\SchoolSettings::TERM }}"
                    name="year_messages"
                    id="year_messages_{{ \App\Classes\Settings\SchoolSettings::TERM }}"
                    @checked($schoolSettings->year_messages == \App\Classes\Settings\SchoolSettings::TERM)
                    aria-describedby="yearMessagesHelp"
            />
            <label class="form-check-label"
                   for="year_messages_{{ \App\Classes\Settings\SchoolSettings::TERM }}">
                {{ __('system.settings.classes.year_messages.term') }}
            </label>
        </div>
        <div class="form-check-inline">
            <input
                    class="form-check-input @error('days') is-invalid @enderror"
                    type="radio"
                    value="{{ \App\Classes\Settings\SchoolSettings::YEAR }}"
                    name="year_messages"
                    id="year_messages_{{ \App\Classes\Settings\SchoolSettings::YEAR }}"
                    @checked($schoolSettings->year_messages == \App\Classes\Settings\SchoolSettings::YEAR)
                    aria-describedby="yearMessagesHelp"
            />
            <label class="form-check-label"
                   for="year_messages_{{ \App\Classes\Settings\SchoolSettings::YEAR }}">
                {{ __('system.settings.classes.year_messages.year') }}
            </label>
        </div>
        <x-utilities.error-display key="days">{{ $errors->first('days') }}</x-utilities.error-display>
        <div id="daysHelp"
             class="form-text">{{ __('system.settings.classes.year_messages.help') }}</div>
    </div>

    <div class="school-setting mb-3">
        <label for="rubrics_max_points">{{ __('system.settings.classes.rubrics_max_points') }}</label>
        <input
                type="number"
                class="form-control @error('rubrics_max_points') is-invalid @enderror"
                id="rubrics_max_points"
                name="rubrics_max_points"
                value="{{ $schoolSettings->rubrics_max_points }}"
                aria-describedby="rubrics_max_pointsHelp"/>
        <x-utilities.error-display key="rubrics_max_points">{{ $errors->first('rubrics_max_points') }}</x-utilities.error-display>
        <div id="rubrics_max_pointsHelp" class="form-text">{{ __('system.settings.classes.rubrics_max_points.help') }}</div>
    </div>

    <div class="row">
        <button type="submit" class="btn btn-primary col">{{ __('system.settings.update') }}</button>
    </div>
</form>