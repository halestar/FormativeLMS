@inject('authSettings','App\Classes\Settings\AuthSettings')
<div class="card mb-5">
    <form action="{{ route('settings.school.update.auth') }}" method="POST">
        <div class="card-body">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col">
                    <div class="school-setting mb-3">
                        <label for="min_password_length"
                               class="form-label">{{ __('settings.auth.min_pass_length') }}</label>
                        <input
                                type="number"
                                class="form-control @error('min_password_length') is-invalid @enderror"
                                id="min_password_length"
                                name="min_password_length"
                                value="{{ $authSettings->min_password_length }}"
                                aria-describedby="min_password_lengthHelp"/>
                        <x-utilities.error-display
                                key="min_password_length">{{ $errors->first('min_password_length') }}</x-utilities.error-display>
                        <div id="min_password_lengthHelp"
                             class="form-text">{{ __('settings.auth.min_pass_length.help') }}</div>
                    </div>
                </div>
                <div class="col">
                    <div class="school-setting">
                        <div class="form-label">{{ __('settings.auth.options') }}</div>
                        <div class="form-check form-check-inline">
                            <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="numbers"
                                    name="numbers"
                                    value="1"
                                    @checked($authSettings->numbers)
                            />
                            <label class="form-check-label" for="numbers">{{ __('settings.auth.numbers') }}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="upper"
                                    name="upper"
                                    value="1"
                                    @checked($authSettings->upper)
                            />
                            <label class="form-check-label" for="upper">{{ __('settings.auth.upper') }}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="symbols"
                                    name="symbols"
                                    value="1"
                                    @checked($authSettings->symbols)
                            />
                            <label class="form-check-label" for="symbols">{{ __('settings.auth.symbols') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-primary col">{{ __('common.update') }}</button>
        </div>
    </form>
</div>
<div class="mt-3">
    <livewire:auth.authentication-priority-manager/>
</div>