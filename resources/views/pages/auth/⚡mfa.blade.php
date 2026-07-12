<?php

use App\Models\People\Person;
use Livewire\Component;

new class extends Component
{
	public string $otp = "";
	public bool $otpError = false;

	public function verifyOtp(): void
	{
		$google2fa = app('pragmarx.google2fa');
		$person = auth()->user();
		if ($google2fa->verifyKey($person->mfa_secret, $this->otp))
		{
			$person->mfa_verified_at = date('Y-m-d');
			$person->save();

			$this->redirectIntended(route('home'));

			return;
		}

		$this->otp = "";
		$this->otpError = true;
	}

	public function render()
	{
		return $this->view()->layout('layouts::guest', ['livewireFullPage' => true]);
	}
};
?>

<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-9 col-lg-7 col-xl-6">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="bg-primary bg-opacity-10 border-bottom">
                    <div class="d-flex align-items-center gap-3 p-4 p-md-5">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white flex-shrink-0"
                             style="width: 3rem; height: 3rem;">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="mb-1 text-dark">{{ __('settings.auth.mfa') }}</h4>
                            <p class="text-body-secondary mb-0">
                                {{ __('settings.auth.mfa.otp.prompt') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if($otpError)
                        <div class="alert alert-danger d-flex align-items-start gap-3 mb-4" role="alert">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 text-danger flex-shrink-0"
                                  style="width: 2.5rem; height: 2.5rem;">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </span>
                            <div>
                                <div class="fw-semibold mb-1">{{ __('settings.auth.mfa.otp.error') }}</div>
                                <div class="small mb-0">{{ __('settings.auth.mfa.otp.error.help') }}</div>
                            </div>
                        </div>
                    @endif

                    <form wire:submit="verifyOtp">
                        <label for="otp" class="form-label fw-semibold">{{ __('settings.auth.mfa.otp') }}</label>
                        <div class="input-group input-group-lg has-validation mb-3">
                            <span class="input-group-text">
                                <i class="fa-solid fa-key"></i>
                            </span>
                            <input
                                    id="otp"
                                    type="text"
                                    inputmode="numeric"
                                    autocomplete="one-time-code"
                                    class="form-control @if($otpError) is-invalid @endif"
                                    wire:model.live="otp"
                                    wire:keydown.enter.prevent="verifyOtp"
                                    autofocus
                                    aria-describedby="otp-help"
                            >
                            <button
                                    type="submit"
                                    class="btn btn-primary"
                                    wire:loading.attr="disabled"
                                    wire:target="verifyOtp"
                            >
                                <span wire:loading.remove wire:target="verifyOtp">
                                    <i class="fa-solid fa-circle-check me-1"></i>
                                    {{ __('common.verify') }}
                                </span>
                                <span wire:loading wire:target="verifyOtp">
                                    <span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>
                                    {{ __('common.verifying') }}
                                </span>
                            </button>
                        </div>

                        <div id="otp-help" class="form-text">
                            {{ __('settings.auth.mfa.otp.help') }}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
