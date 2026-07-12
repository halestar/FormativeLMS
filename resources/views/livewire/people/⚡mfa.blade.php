<?php

use App\Classes\IdCard\BarcodeGenerator;
use App\Models\People\Person;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
	public Person $person;
	public bool $mfaEnabled;
	public bool $mfaEnabling = false;
	public bool $showQr = false;
	public ?string $mfaSecret = null;
	public string $otp = "";


	public function mount()
	{
		$this->person = auth()->user();
		$this->mfaEnabled = $this->person->mfa_enabled;
	}

	#[Computed]
	public function qrCode(): ?string
	{
		if ($this->showQr && $this->mfaSecret !== null)
		{
			$google2fa = app('pragmarx.google2fa');
			$qrCodeUrl = $google2fa->getQRCodeUrl(
				$this->person->name,
				$this->person->email,
				$this->mfaSecret
			);
			$qr = new BarcodeGenerator($qrCodeUrl);
			$qr->barcodeType = "qr";
			$qr->height = 300;
			$qr->width = 300;
			return $qr->toSVG();
		}
		return null;
	}

	public function enablingMfa()
	{
		$this->mfaEnabling = true;
		$google2fa = app('pragmarx.google2fa');
		$this->mfaSecret = $google2fa->generateSecretKey();
		Log::info("mfa is " . $this->mfaSecret);
		$this->showQr = true;
		$this->otp = "";
	}

	public function verifyOtp()
	{
		$google2fa = app('pragmarx.google2fa');
		if ($google2fa->verifyKey($this->mfaSecret, $this->otp))
		{
			$this->person->mfa_secret = $this->mfaSecret;
			$this->person->mfa_enabled = true;
			$this->person->mfa_verified_at = null;
			$this->person->save();
			$this->mfaEnabled = true;
			$this->showQr = false;
			$this->mfaEnabling = false;
			$this->otp = "";
		}
		else
		{
			$this->otp = "";
			$this->addError("otp", "THe code you provided is incorrect. Please try again");
		}
	}

	public function disable()
	{
		$this->person->mfa_secret = null;
		$this->person->mfa_enabled = false;
		$this->person->mfa_verified_at = null;
		$this->person->save();
		$this->mfaEnabled = false;
		$this->showQr = false;
		$this->mfaEnabling = false;
		$this->otp = "";
	}
};
?>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="bg-primary bg-opacity-10 border-bottom">
        <div class="d-flex align-items-center gap-3 p-4 p-md-5">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white flex-shrink-0"
                 style="width: 3rem; height: 3rem;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="min-w-0 flex-grow-1">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h4 class="mb-0 text-dark">{{ __('settings.auth.mfa') }}</h4>
                    @if($mfaEnabled)
                        <span class="badge text-bg-success bg-opacity-100">{{ __('common.enabled') }}</span>
                    @else
                        <span class="badge text-bg-warning bg-opacity-100">{{ __('common.disabled') }}</span>
                    @endif
                </div>
                <p class="text-body-secondary mb-0">
                    {{ __('settings.auth.mfa.help') }}
                </p>
            </div>
        </div>
    </div>

    <div class="card-body p-4 p-md-5">
        @if($mfaEnabling)
            <div class="alert alert-primary d-flex align-items-start gap-3 mb-4" role="alert">
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary flex-shrink-0"
                      style="width: 2.5rem; height: 2.5rem;">
                    <i class="fa-solid fa-qrcode"></i>
                </span>
                <div>
                    <div class="fw-semibold mb-1">{{ __('settings.auth.mfa.setup') }}</div>
                    <div class="small mb-0">{{ __('settings.auth.mfa.setup.help') }}</div>
                </div>
            </div>

            <div class="border rounded-3 bg-body-tertiary p-3 p-md-4 text-center h-100">
                <div class="small text-body-secondary text-uppercase fw-semibold mb-3">{{ __('settings.auth.mfa.qr') }}</div>

                <div class="d-inline-flex align-items-center justify-content-center bg-white border rounded-3 shadow-sm p-3 mb-4">
                    {!! $this->qrCode !!}
                </div>

                <div class="small text-body-secondary text-uppercase fw-semibold mb-2">{{ __('settings.auth.mfa.secret') }}</div>
                <code class="d-inline-block user-select-all text-break bg-body rounded-2 border px-3 py-2">
                    {{ $mfaSecret }}
                </code>
                <div>
                    <div class="input-group input-group-lg has-validation mb-2">
                            <span class="input-group-text">
                                <i class="fa-solid fa-key"></i>
                            </span>
                        <input
                                id="mfa-otp"
                                type="text"
                                inputmode="numeric"
                                autocomplete="one-time-code"
                                class="form-control @error('otp') is-invalid @enderror"
                                wire:model="otp"
                                wire:keydown.enter="verifyOtp"
                                aria-describedby="mfa-otp-help"
                        >
                        <button
                                type="button"
                                class="btn btn-primary"
                                wire:click="verifyOtp"
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

                    <x-utilities.error-display key="otp">{{ $errors->first('otp') }}</x-utilities.error-display>

                    <div id="mfa-otp-help" class="form-text">
                        {{ __('settings.auth.mfa.otp.prompt') }}
                    </div>
                </div>
            </div>
        @elseif($mfaEnabled)
            <div class="border rounded-3 bg-body-tertiary p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success flex-shrink-0"
                              style="width: 2.75rem; height: 2.75rem;">
                            <i class="fa-solid fa-circle-check"></i>
                        </span>
                        <div>
                            <h5 class="mb-1">{{ __('settings.auth.mfa.enabled') }}</h5>
                            <p class="text-body-secondary mb-0">
                                {{ __('settings.auth.mfa.enabled.help') }}
                            </p>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="button"
                                class="btn btn-outline-warning"
                                wire:click="enablingMfa"
                        >
                            <i class="fa-solid fa-rotate me-1"></i>
                            {{ __('settings.auth.mfa.setup.new') }}
                        </button>
                        <button type="button"
                                class="btn btn-outline-danger"
                                wire:click="disable"
                                wire:confirm="{{ __('settings.auth.mfa.disabled.confirm') }}"
                        >
                            <i class="fa-solid fa-shield-halved me-1"></i>
                            {{ __('common.disable') }}
                        </button>
                    </div>
                </div>
            </div>
        @else
            <div class="border rounded-3 bg-body-tertiary p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10 text-warning flex-shrink-0"
                              style="width: 2.75rem; height: 2.75rem;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </span>
                        <div>
                            <h5 class="mb-1">{{ __('settings.auth.mfa.disabled') }}</h5>
                            <p class="text-body-secondary mb-0">
                                {{ __('settings.auth.mfa.disabled.help') }}
                            </p>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary btn-lg" wire:click="enablingMfa">
                        <i class="fa-solid fa-shield-halved me-2"></i>
                        {{ __('common.enable') }}
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
