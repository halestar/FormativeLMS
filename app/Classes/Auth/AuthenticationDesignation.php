<?php

namespace App\Classes\Auth;

use App\Models\People\Person;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Serializable;

class AuthenticationDesignation implements Arrayable, JSONSerializable
{
	public const DEFAULT_PRIORITY = 0;
	public function __construct
		(
			public int $priority,
			public array $roles,
			public array|string $authModules
		)
	{}


	public function toArray(): array
	{
		return
		[
			'priority' => $this->priority,
			'roles' => $this->roles,
			'auth_modules' => $this->authModules,
		];
	}

	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}

	public static function hydrate(array $data): AuthenticationDesignation
	{
		$priority = $data['priority'];
		$roles = $data['roles'];
		$authModules = $data['auth_modules'];
		$auth = new AuthenticationDesignation($priority, $roles, $authModules);
		return $auth;
	}

	public static function makeDefaultDesignation($priority = self::DEFAULT_PRIORITY): AuthenticationDesignation
	{
		$roles = [];
		$authModules = [LocalAuthenticator::driverName()];
		return new AuthenticationDesignation($priority, $roles, $authModules);
	}

	public function appliesToPerson(Person $person)
	{
		return ($this->priority === self::DEFAULT_PRIORITY) || ($person->hasAnyRole($this->roles));
	}

	public function prettyModules():string
	{
		if(is_array($this->authModules))
		{
			return implode(", ", array_map(fn($value) => config('auth.drivers.' . $value . '.class')::driverPrettyName(), $this->authModules));
		}
		return config('auth.drivers.' . $this->authModules . '.class')::driverPrettyName();
	}
}