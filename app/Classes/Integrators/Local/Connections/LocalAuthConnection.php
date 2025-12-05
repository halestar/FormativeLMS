<?php

namespace App\Classes\Integrators\Local\Connections;

use App\Models\Integrations\Connections\AuthConnection;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Hash;

class LocalAuthConnection extends AuthConnection
{
	use ThrottlesLogins;

	protected static array $instanceDefaults =
		[
			'password' => null,
			'must_change_password' => false,
		];
	protected int $maxAttempts;
	protected int $decayMinutes;
	
	public static function requiresPassword(): bool
	{
		return true;
	}
	
	public static function requiresRedirection(): bool
	{
		return false;
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
	
	public static function callback(): \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse|null
	{
		return null;
	}
	
	protected static function booted(): void
	{
		static::retrieved(function(LocalAuthConnection $connection)
		{
			$connection->maxAttempts = $connection->service->data->maxAttempts;
			$connection->decayMinutes = $connection->service->data->decayMinutes;
		});
	}
	
	public function username()
	{
		return 'email';
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
		$this->data->password = $newPassword;
		$this->save();
		return true;
	}
	
	public function attemptLogin(string $password, bool $rememberMe, bool $autoLogin = true): bool
	{
		//if we're not connected or locked, or the password is not set, then we can't log in.
		if($this->isLocked() || !$password || !$this->data->password) return false;
		//is the password correct?
		if(Hash::check($password, $this->data->password))
		{
			$this->clearLoginAttempts(request());
			return true;
		}
		//in this case the user did NOT enter the correct password, so we flag it.
		$this->incrementLoginAttempts(request());
		//has there been enough login attempts?
		if($this->hasTooManyLoginAttempts(request()))
			$this->lockUser(true, Carbon::now()
			                            ->addMinutes($this->data->lockoutTimeout));
		return false;
	}
	
	public function canSetMustChangePassword(): bool
	{
		return true;
	}
	
	public function setMustChangePassword(bool $mustChangePassword = true): void
	{
		$this->data->must_change_password = $mustChangePassword;
		$this->save();
	}
	
	public function mustChangePassword(): bool
	{
		return $this->data->must_change_password;
	}
	
	public function verifyPassword(string $password): bool
	{
		if(!$password || !$this->data->password) return false;
		return (Hash::check($password, $this->data->password));
	}
	
	public function redirect(): \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse|null
	{
		return null;
	}
}