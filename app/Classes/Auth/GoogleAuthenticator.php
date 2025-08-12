<?php

namespace App\Classes\Auth;

use App\Models\People\Person;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cookie;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthenticator extends Authenticator
{
	protected array $settingSkeleton =
		[
			'google_id' => null,
			'avatar' => null,
			'avatar_original' => null,
			'locked' => false,
			'locked_until' => null,
		];

	public static function driverName(): string
	{
		return 'google';
	}

	public static function driverPrettyName(): string
	{
		return __('auth.google.pretty');
	}

	public static function driverDescription(): string
	{
		return __('auth.google.description');
	}

	public static function requiresPassword(): bool
	{
		return false;
	}

	public static function requiresRedirection(): bool
	{
		return true;
	}

	public function redirect(): \Illuminate\Http\RedirectResponse | \Symfony\Component\HttpFoundation\RedirectResponse | null
	{
		return Socialite::driver('google')
			->with(['login_hint' => $this->user->system_email])
			->redirect();
	}

	public static function callback(): \Illuminate\Http\RedirectResponse | \Symfony\Component\HttpFoundation\RedirectResponse | null
	{
		$gUser = Socialite::driver('google')->user();
		//is there a user with this email?
		$user = Person::where('email', $gUser->email)->first();
		if(!$user)
		{
			//if there's no user, we go back to the login place
			return redirect()->route('login');
		}
		//since the user exists, save some data
		$authDriver = $user->auth_driver;
		$settings = $authDriver->getPasswordSettings();
		$settings['google_id'] = $gUser->id;
		$settings['avatar'] = $gUser->avatar;
		$settings['avatar_original'] = $gUser->avatar_original;
		$authDriver->setPasswordSettings($settings);
		//do we have a cookie that remembers us?
		$rememberMe = Cookie::has('remember-me');
		//login the user.
		auth()->login($user, $rememberMe);
		//and go home
		return redirect()->route('home');
	}

	public static function loginButton(): string
	{
		$html = '<img alt="Sign in with Google" src="' . asset('/images/auth/web_neutral_rd_SI.svg') . '" /></a>';
		return Blade::render($html);
	}
}