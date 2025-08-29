<?php

namespace App\Classes\Auth;

use App\Models\People\Person;
use App\Models\Utilities\SystemSetting;
use Carbon\Carbon;

abstract class Authenticator
{
	protected Person $user;
	public function __construct(Person $user)
	{
		$this->user = $user;
	}

	protected array $settingSkeleton =
		[
			'locked' => false,
			'locked_until' => null,
		];
	
	public function getPasswordSettings(): array
	{
		$settingKey = "authenticators." . static::driverName() . "." . $this->user->id;
		$setting = SystemSetting::find($settingKey);
		if($setting && is_array($setting->value))
			return $setting->value;
		return $this->settingSkeleton;
	}

	protected function setPasswordSettings(array $newSettings): void
	{
		$settingKey = "authenticators." . static::driverName() . "." . $this->user->id;
		$setting = SystemSetting::find($settingKey);
		if($setting)
			$setting->value = $newSettings;
		else
		{
			$setting = new SystemSetting();
			$setting->name = $settingKey;
			$setting->value = $newSettings;
		}
		$setting->save();
	}

	/**
	 * @return string The driver name in the config section
	 */
	abstract public static function driverName(): string;

	/**
	 * @return string The "pretty" name of the driver, so we can show it in delect boxes.
	 */
	abstract public static function driverPrettyName(): string;

	/**
	 * @return string The driver description, for help when determining the type of auth to use.
	 */
	abstract public static function driverDescription(): string;

	/**
	 * @return bool If set to true, then the login system will get a password from the user to pass
	 * to this authenticator
	 */
	abstract public static function requiresPassword(): bool;

	/**
	 * @return string A html-string of the button for the user to login with. This will only
	 * really be done when the user chooses their login method. It should not be a link, as it will be
	 * wrapped by one.
	 */
	abstract static public function loginButton(): string;

	/**
	 * @return bool If set to true, the Login system will redirect the user to another page that will
	 * handle the authentication.
	 */
	abstract public static function requiresRedirection(): bool;

	/**************************************************************
	 * PASSWORD FUNCTIONS
	 */

	/**
	 * @return bool Whether this authenticator can be assigned a password by an
	 * administrator
	 */
	public function canChangePassword(): bool
	{
		return false;
	}

	/**
	 * @return bool Whether the user can reset their own password.
	 */
	public function canResetPassword(): bool
	{
		return false;
	}

	/**
	 * This function will attempt to change a password for this authenticator.
	 * @param string $password THe new password
	 * @return bool Whether the operation was successful.
	 */
	public function setPassword(string $password): bool
	{
		return false;
	}

	/**
	 * This function will attempt to authenticate the user with a provided
	 *   password.
	 * @param string $password The password from the login form
	 * @return bool Whether the user was successfully logged in.
	 */
	public function attemptLogin(string $password, bool $rememberMe): bool
	{
		return false;
	}

	/**
	 * @param string $password The password to test
	 * @return bool Whther the current password matches the one provided.
	 */
	public function verifyPassword(string $password): bool
	{
		return false;
	}

	/**
	 * @return bool Whether this authenticator can set if user must change their password.
	 */
	public function canSetMustChangePassword(): bool
	{
		return false;
	}

	/**
	 * Sets whether the user
	 * @param bool $mustChangePassword Optional, whether the password must be changed. Defaults to true
	 * @return void
	 */
	public function setMustChangePassword(bool $mustChangePassword = true): void {}

	/**
	 * @return bool Returns whether the user must change their password.
	 */
	public function mustChangePassword(): bool
	{
		return false;
	}


	/**************************************************************
	 * USER LOCKING FUNCTIONS
	 */

	/**
	 * @param bool $locked Whether to lock this user. Defaults to true
	 * @param Carbon|null $timeout Until when to lock this user. Default is null, which is until manually unlocked.
	 * @return void
	 */
	public function lockUser(bool $locked = true, Carbon $until = null): void
	{
		$settings = $this->getPasswordSettings();
		$settings['locked'] = $locked;
		if($until && $locked)
			$settings['locked_until'] = $until->toDateTimeString();
		else
			$settings['locked_until'] = null;
		$this->setPasswordSettings($settings);
	}

	/**
	 * @return bool Whether this user is locked.
	 */
	public function isLocked(): bool
	{
		$settings = $this->getPasswordSettings();
		if(!$settings['locked'])
			return false;
		if(!$settings['locked_until'])
			return true;
		//else, we need to determine if the time has past
		if(Carbon::now()->isAfter($settings['locked_until']))
		{
			$settings['locked'] = false;
			$settings['locked_until'] = null;
			$this->setPasswordSettings($settings);
			return false;
		}
		return true;
	}

	/**
	 * @return Carbon|null Returns the Carbon date that this is locked until, or null if foreever.
	 */
	public function lockedUntil(): ?Carbon
	{
		if($this->isLocked())
		{
			$settings = $this->getPasswordSettings();
			return $settings['locked_until']? Carbon::parse($settings['locked_until']): null;
		}
		return null;
	}

	/**************************************************************
	 * REDIRECTION FUNCTIONS
	 */
	public function redirect(): \Illuminate\Http\RedirectResponse | \Symfony\Component\HttpFoundation\RedirectResponse | null
	{
		return null;
	}
	public static function callback(): \Illuminate\Http\RedirectResponse | \Symfony\Component\HttpFoundation\RedirectResponse | null
	{
		return null;
	}

	public static function all(): array
	{
		$modules = [];
		foreach(config('auth.drivers') as $name => $driver)
		{
			$modules[$name] = $driver['class'];
		}
		return $modules;
	}

}