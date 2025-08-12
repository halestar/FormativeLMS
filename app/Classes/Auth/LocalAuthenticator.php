<?php

namespace App\Classes\Auth;

use App\Models\People\Person;
use App\Models\Utilities\SystemSetting;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Hash;

class LocalAuthenticator extends Authenticator
{
	use ThrottlesLogins;
	protected int $maxAttempts = 5;
	protected int $decayMinutes = 1;
	protected array $settingSkeleton =
		[
			'password' => null,
			'must_change_password' => false,
			'locked' => false,
			'locked_until' => null,
		];


	public function username()
	{
		return 'email';
	}

	public function __construct(Person $user)
	{
		parent::__construct($user);
		$this->maxAttempts = config('auth.drivers.local.max_attempts');
		$this->decayMinutes = config('auth.drivers.local.lockout_decay');
	}


	public static function driverName(): string
	{
		return "local";
	}

	public static function driverPrettyName(): string
	{
		return __('auth.local.pretty');
	}

	public static function driverDescription(): string
	{
		return __('auth.local.description');
	}

	public static function requiresPassword(): bool
	{
		return true;
	}

	public static function requiresRedirection(): bool
	{
		return false;
	}

	public function canChangePassword(): bool
	{
		return true;
	}

	public function canResetPassword(): bool
	{
		return true;
	}

	public function setPassword(string $password): bool
	{
		$newPassword = Hash::make($password);
		$settings = $this->getPasswordSettings();
		$settings['password'] = $newPassword;
		$this->setPasswordSettings($settings);
		return true;
	}

	public function attemptLogin(string $password, bool $rememberMe): bool
	{
		if($this->isLocked())
			return false;
		$settings = $this->getPasswordSettings();
		//if the passed password OR the stored password is blank, then you can't login
		if(!$password || !$settings['password'])
			return false;
		//is the password correct?
		if(Hash::check($password, $settings['password']))
		{
			//log the person in
			Auth::guard()->login($this->user, $rememberMe);
			//regenerate the session
			request()->session()->regenerate();
			$this->clearLoginAttempts(request());
			return true;
		}
		//in this case the user did NOT enter the correct password, so we flag it.
		$this->incrementLoginAttempts(request());
		//has there been enough login attempts?
		if($this->hasTooManyLoginAttempts(request()))
			$this->lockUser(true, Carbon::now()->addMinutes(config('auth.drivers.local.lockout_timeout')));
		return false;
	}

	public function canSetMustChangePassword(): bool
	{
		return true;
	}

	public function setMustChangePassword(bool $mustChangePassword = true): void
	{
		$settings = $this->getPasswordSettings();
		$settings['must_change_password'] = $mustChangePassword;
		$this->setPasswordSettings($settings);
	}

	public function mustChangePassword(): bool
	{
		$settings = $this->getPasswordSettings();
		return $settings['must_change_password'];
	}

	public static function loginButton(): string
	{
		$html = <<< EOHTML
<div class="border border-dark rounded-4 text-bg-secondary fs-5 p-2 fw-bolder">
	<span class="pe-2 me-2"><i class="fa-solid fa-right-to-bracket"></i></span>
	Login Locally
</div>
EOHTML;
		return Blade::render($html);
	}

	public function verifyPassword(string $password): bool
	{
		$settings = $this->getPasswordSettings();
		if(!$password || !$settings['password'])
			return false;
		//is the password correct?
		if(Hash::check($password, $settings['password']))
			return true;
		return false;
	}


}