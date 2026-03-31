<div class="container">
    @use('App\Enums\Auth\LoginStages');
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7 col-xl-6">
            <div class="card border-0 shadow-sm">
                @if($magicLinkSent)
                    <div class="card-body">
                        <div class="alert alert-success mb-0 rounded-4 border-0 shadow-sm" role="alert">
                            <div class="d-flex align-items-start gap-3">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success flex-shrink-0" style="width: 2.75rem; height: 2.75rem;">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                                <div>
                                    <h4 class="alert-heading h5 mb-2">{{ __('Check your email') }}</h4>
                                    <p class="mb-0">{{ __('A login link was sent to :email. Please check your inbox to continue.', ['email' => $email]) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card-header bg-white border-0 pb-0">
                        <div class="d-flex flex-column gap-1">
                            <h3 class="card-title mb-0">{{ __('Login') }}</h3>
                            <p class="text-body-secondary mb-0">{{ __('Use your account credentials to continue.') }}</p>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(in_array($stage, [LoginStages::PromptEmail, LoginStages::PromptPassword], true))
                            <div class="mb-4">
                                <x-authenticate-passkey>
                                    <button type="button" class="btn btn-outline-primary btn-lg w-100 rounded-4 px-4 py-3 shadow-sm">
                                        <span class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 text-start">
                                            <span>
                                                <span class="d-block fw-semibold">{{ __('passkeys::passkeys.authenticate_using_passkey') }}</span>
                                                <span class="d-block small text-body-secondary">{{ __('Skip typing your password when a saved passkey is available.') }}</span>
                                            </span>
                                            <span class="badge rounded-pill text-bg-primary px-3 py-2 align-self-start align-self-sm-center">{{ __('common.next') }}</span>
                                        </span>
                                    </button>
                                </x-authenticate-passkey>
                            </div>

                            <div class="d-flex align-items-center text-body-secondary text-uppercase small fw-semibold mb-4">
                                <span class="flex-grow-1 border-top"></span>
                                <span class="px-3">{{ __('or') }}</span>
                                <span class="flex-grow-1 border-top"></span>
                            </div>
                        @endif

                        @switch($stage)
                            @case(LoginStages::BlockedLogin)
                                <div class="alert alert-danger">
                                    {{ __('errors.auth.account') }}
                                </div>
                            @break
                            @case(LoginStages::LockedUser)
                                <div class="alert alert-danger">
                                    @if(!$lockedUntil)
                                        {{ __('errors.auth.locked.admin') }}
                                    @else
                                        {{ __('errors.auth.locked', ['time' => $lockedUntil->diffForHumans()]) }}
                                    @endif
                                </div>
                            @break
                            @case(LoginStages::PromptEmail)
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
                                                autocomplete="username"
                                                autofocus
                                                aria-label="{{ __('people.profile.fields.email') }}"
                                                aria-describedby="email-span"
                                                wire:model="email"
                                        >
                                        <x-utilities.error-tooltip
                                                key="email">{{ $errors->first('email') }}</x-utilities.error-tooltip>
                                        <button class="btn btn-primary" type="submit">{{ __('common.next') }}</button>
                                    </div>

                                    <div class="row mb-4">
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

                                    <div class="rounded-4 border bg-light p-4">
                                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                            <div>
                                                <h4 class="h6 mb-1">{{ __('Send me a magic link') }}</h4>
                                                <p class="text-body-secondary small mb-0">{{ __('Enter your email address and we will send you a secure sign-in link.') }}</p>
                                            </div>
                                            <button
                                                    type="button"
                                                    class="btn btn-outline-secondary"
                                                    wire:click="sendMagicLink"
                                            >{{ __('Email login link') }}</button>
                                        </div>
                                    </div>
                                </form>
                            @break
                            @case(LoginStages::PromptPassword)
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
                                            autocomplete="username"
                                    />
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
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                        <button
                                                type="button"
                                                class="btn btn-outline-secondary order-2 order-md-1"
                                                wire:click="sendMagicLink"
                                        >{{ __('Email login link instead') }}</button>
                                        <div class="d-flex justify-content-end order-1 order-md-2">
                                            <button type="submit" class="btn btn-primary mx-2">
                                                {{ __('Login') }}
                                            </button>
                                            <button
                                                    type="button"
                                                    class="btn btn-danger mx-2"
                                                    wire:click="set('stage', '{{ LoginStages::PromptEmail }}')"
                                            >{{ __('common.cancel') }}</button>
                                        </div>
                                    </div>
                                </form>
                            @break
                            @case(LoginStages::PromptMethod)
                                <h3>Select a sign-in method for {{ $user->system_email }}</h3>
                                <div class="d-flex flex-column align-items-center">
                                    @foreach($methodOptions as $service_id => $button)
                                        <div class="my-2 show-as-action" wire:click="submitMethod({{ $service_id }});">
                                            {!! $button !!}
                                        </div>
                                    @endforeach
                                </div>
                            @break
                            @case(LoginStages::CodeVerification)
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
                            @break
                            @case(LoginStages::CodeTimeout)
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
                            @break
                            @case(LoginStages::ResetPassword)
                                <h4>{{ __('settings.auth.password.reset.for', ['user' => $user->system_email]) }}</h4>
                                <livewire:auth.change-password-form :person="$user" :auth-first="false"/>
                            @break
                        @endswitch
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>