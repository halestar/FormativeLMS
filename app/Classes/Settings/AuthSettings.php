<?php

namespace App\Classes\Settings;

use App\Casts\Utilities\SystemSettings\AuthPriorities;
use App\Classes\Auth\AuthenticationDesignation;
use App\Models\People\Person;
use App\Models\Utilities\SystemSetting;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AuthSettings extends SystemSetting
{
	protected function casts():array
	{
		return [
			'priorities' => AuthPriorities::class,
		];
	}

	protected static string $settingKey = "auth";

	protected static function defaultValue(): array
	{
		return
			[
				"min_password_length" => 8,
				"numbers" => true,
				"upper" => true,
				"symbols" => true,
				"priorities" => [AuthenticationDesignation::makeDefaultDesignation()],
			];
	}

	public function minPasswordLength(): Attribute
	{
		return $this->basicProperty('min_password_length');
	}
	public function numbers(): Attribute
	{
		return $this->basicProperty('numbers');
	}
	public function upper(): Attribute
	{
		return $this->basicProperty('upper');
	}
	public function symbols(): Attribute
	{
		return $this->basicProperty('symbols');
	}

	public static function applyAuthenticationPriorities()
	{
		//we reset all the auth to null
		Person::query()->update(['auth_driver' => null]);
	}

	public function determineAuthentication(Person $person): string | array
	{
		$priorities = $this->priorities;
		//determine the default priority and assign it.
		$defaultPriority = $priorities[0]?? AuthenticationDesignation::makeDefaultDesignation();
		$authenticator = $defaultPriority->authModules;
		//next we through all the priorities starting at 1 (if they exists), and exit when we
		//find the first priority that this user applies to.
		for($i = 1; $i < count($priorities); $i++)
		{
			if($priorities[$i]->appliesToPerson($person))
			{
				$authenticator = $priorities[$i]->authModules;
				break;
			}
		}
		return $authenticator;
	}

}