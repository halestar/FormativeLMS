<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    @if($accountError)
                        <div class="alert alert-danger">
                            {{ __('errors.auth.account') }}
                        </div>
                    @else
                        @if($promptEmail)
                            @if($lockedUser)
                                <div class="alert alert-danger">
                                    @if(!$lockedUntil)
                                        {{ __('errors.auth.locked.admin') }}
                                    @else
                                        {{ __('errors.auth.locked', ['time' => $lockedUntil->diffForHumans()]) }}
                                    @endif
                                </div>
                            @endif
                            <form wire:submit="submitEmail">
                                <div class="input-group mb-3 has-validation">
                                    <span class="input-group-text"
                                          id="email-span">{{ __('people.profile.fields.email') }}</span>
                                    <input
                                            id="email"
                                            type="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            name="email"
                                            required
                                            autocomplete="email"
                                            autofocus
                                            aria-label="{{ __('people.profile.fields.email') }}"
                                            aria-describedby="email-span"
                                            wire:model="email"
                                    >
                                    <x-utilities.error-tooltip
                                            key="email">{{ $errors->first('email') }}</x-utilities.error-tooltip>
                                    <button class="btn btn-primary" type="submit">{{ __('common.next') }}</button>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="remember"
                                                    id="remember"
                                                    wire:model="rememberMe"
                                            >

                                            <label class="form-check-label" for="remember">
                                                {{ __('Remember Me') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @elseif($promptPassword)
                            <div class="input-group mb-3">
                                <span class="input-group-text"
                                      id="email-span">{{ __('people.profile.fields.email') }}</span>
                                <input
                                        type="email"
                                        class="form-control disabled"
                                        aria-label="{{ __('people.profile.fields.email') }}"
                                        aria-describedby="email-span"
                                        value="{{ $email }}"
                                        disabled
                                        readonly
                                >
                            </div>
                            <form wire:submit="submitPassword">
                                <div class="input-group mb-3" x-data x-init="$refs.password.focus()">
                                    <span id="password-span" class="input-group-text">{{ __('Password') }}</span>
                                    <input
                                            id="password"
                                            type="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            name="password"
                                            required
                                            autocomplete="current-password"
                                            wire:model="password"
                                            x-ref="password"
                                    />
                                    <x-utilities.error-tooltip
                                            key="password">{{ $errors->first('password') }}</x-utilities.error-tooltip>
                                    @if($canResetPassword)
                                        <button
                                                type="button"
                                                class="btn btn-warning"
                                                wire:click="forgotPassword"
                                        >{{ __('auth.forgot') }}</button>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary mx-2">
                                        {{ __('Login') }}
                                    </button>
                                    <button
                                            type="button"
                                            class="btn btn-danger mx-2"
                                            wire:click="returnToEmail"
                                    >{{ __('common.cancel') }}</button>
                                </div>
                            </form>
                        @elseif($promptMethod)
                            <h3>Select a sign-in method for {{ $user->system_email }}</h3>
                            <div class="d-flex flex-column align-items-center">
                                @foreach($methodOptions as $service_id => $button)
                                    <div class="my-2 show-as-action" wire:click="submitMethod({{ $service_id }});">
                                        {!! $button !!}
                                    </div>
                                @endforeach
                            </div>
                        @elseif($codeVerification)
                            <form wire:submit="submitVerification">
                                <div class="alert alert-info mb-3">
                                    {{ __('settings.auth.verify.instructions', ['email' => $user->system_email, 'time' => config('lms.auth_code_timeout')]) }}
                                </div>
                                <div
                                        class="d-flex justify-content-center"
                                        x-data="
                                        {
                                            seconds: {{ config('lms.auth_code_timeout') * 60 }},
                                            intervalId: null,
                                            init()
                                            {
                                                if (this.intervalId === null) {
                                                    this.intervalId = setInterval(() => {
                                                        this.seconds--;
                                                        if(this.seconds <= 0) {
                                                            clearInterval(this.intervalId);
                                                            this.intervalId = null;
                                                            $wire.timeoutTimer();
                                                        }
                                                    }, 1000);
                                                }
                                            },
                                            get formattedTime() {
                                                const minutes = Math.floor((this.seconds % 3600) / 60);
                                                const remainingSeconds = this.seconds % 60;

                                                return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
                                            }
                                        }
                                        "
                                >
                                    <span x-text="formattedTime" class="display-3"></span>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text fs-1">{{ __('settings.auth.verify.code') }}</span>
                                    <input
                                            type="text"
                                            wire:model="userAuthCode"
                                            class="form-control fs-1"
                                    />
                                    <button type="submit" class="btn btn-success fs-1">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger fs-1"
                                            wire:click="gotoStage('promptPassword')">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </div>
                                @error('userAuthCode')
                                <div class="alert alert-danger mt-3">
                                    {{ $errors->first('userAuthCode') }}
                                </div>
                                @enderror
                            </form>
                        @elseif($codeTimeout)
                            <div class="alert alert-danger">
                                {{ __('errors.auth.verification.timeout') }}
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-primary mx-2" wire:click="forgotPassword">
                                    {{ __('settings.auth.verify.request') }}
                                </button>
                                <button type="button" class="btn btn-secondary mx-2"
                                        wire:click="gotoStage('submitEmail')">
                                    {{ __('common.cancel') }}
                                </button>
                            </div>
                        @elseif($resetPassword)
                            <h4>{{ __('settings.auth.password.reset.for', ['user' => $user->system_email]) }}</h4>
                            <livewire:auth.change-password-form :person="$user" :auth-first="false"/>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>