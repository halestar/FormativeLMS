<?php

namespace App\Http\Controllers\Auth;

use App\Models\Integrations\Connections\AuthConnection;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\LaravelPasskeys\Http\Controllers\AuthenticateUsingPasskeyController;

class PasskeyController extends AuthenticateUsingPasskeyController
{
	protected $redirectTo = '/home';

	protected function logInAuthenticatable(Authenticatable $authenticatable, bool $remember = false): self
	{
		$this->redirectTo = AuthConnection::completeLogin($authenticatable);
		return $this;
	}

	protected function validPasskeyResponse(Request $request): RedirectResponse
	{
		return redirect($this->redirectTo);
	}
}
