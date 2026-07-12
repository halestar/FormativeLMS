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
            <div class="row mb-3">
                <div class="col">
                    <div class="school-setting">
                        <div class="form-label">{{ __('settings.auth.passkeys') }}</div>
                        <div class="form-check form-check-inline">
                            <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="passkeys_allow"
                                    name="passkeys_allow"
                                    value="1"
                                    @checked($authSettings->passkeys_allow)
                            />
                            <label class="form-check-label"
                                   for="passkeys_allow">{{ __('settings.auth.passkeys.allow') }}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="passkeys_require"
                                    name="passkeys_require"
                                    value="1"
                                    @disabled(!$authSettings->passkeys_allow)
                                    @checked($authSettings->passkeys_allow && $authSettings->passkeys_require)
                            />
                            <label class="form-check-label"
                                   for="passkeys_require">{{ __('settings.auth.passkeys.require') }}</label>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col">
                    <div class="school-setting">
                        <div class="form-label">{{ __('settings.auth.mfa.options') }}</div>
                        <div class="d-flex flex-wrap justify-content-start align-items-center gap-3">
                            <div class="form-check form-check-inline">
                                <span class="form-label">{{ __('settings.auth.mfa.force') }}</span>
                                <input
                                        type="checkbox"
                                        class="form-check-input"
                                        id="mfa_force"
                                        name="mfa_force"
                                        value="1"
                                        @checked($authSettings->mfa_force)
                                />
                            </div>
                            <div class="form-check form-check-inline">
                                <input
                                        type="checkbox"
                                        class="form-check-input"
                                        id="mfa_force_admins"
                                        name="mfa_force_admins"
                                        value="1"
                                        @checked($authSettings->mfa_force_admins)
                                />
                                <label class="form-check-label"
                                       for="mfa_force_admins">{{ __('settings.auth.mfa.force.admin') }}</label>
                            </div>
                            <div class="flex-shrink-1">
                                <div class="input-group">
                                    <span class="input-group-text">{{ __('settings.auth.mfa.timeout') }}</span>
                                    <input type="number"
                                           class="form-control"
                                           id="mfa_timeout_days"
                                           name="mfa_timeout_days"
                                           value="{{ $authSettings->mfa_timeout_days }}"
                                    />
                                    <span class="input-group-text">{{ trans_choice('common.day', $authSettings->mfa_timeout_days) }}</span>
                                </div>
                            </div>
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