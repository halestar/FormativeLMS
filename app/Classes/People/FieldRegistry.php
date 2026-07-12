<?php

namespace App\Classes\People;

use App\Models\Utilities\SchoolRoles;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FieldRegistry
{
	protected array $fields = [];
	protected array $intrinsicFields = [];
	protected array $dynamicFields = [];

	public function getIntrinsicFields(): array
	{
		if (empty($this->intrinsicFields))
		{
			$this->intrinsicFields = config('fields');

			foreach ($this->intrinsicFields as $key => $translationKey)
				$this->intrinsicFields[$key] = __($translationKey);
		}

		return $this->intrinsicFields;
	}

	public function getDynamicFields(?array $only = null): array
	{
		if (empty($this->dynamicFields))
		{
			$this->dynamicFields = [];
			if ($only)
				$roles = SchoolRoles::whereIn('name', $only)->get();
			else
				$roles = SchoolRoles::all();
			foreach ($roles as $role)
			{
				if (empty($role->fields))
					continue;
				foreach ($role->fields as $field)
				{
					$key = "role_fields." . $field->fieldId;
					$this->dynamicFields[$key] = $field->fieldName;
				}
			}
		}
		return $this->dynamicFields;
	}

	public function all(): array
	{
		if (empty($this->fields))
		{
			$this->fields = array_merge(
				$this->getIntrinsicFields(),
				$this->getDynamicFields()
			);
		}

		return $this->fields;
	}

	public function forRoles(string|SchoolRoles|int|array|Collection $roles): array
	{
		//convert all to string.
		$toName = function (string|SchoolRoles|int $role): string
		{
			if (is_string($role))
				return Str::snake($role);
			if ($role instanceof SchoolRoles)
				return Str::snake($role->name);

			return Str::snake(SchoolRoles::find($role)?->name ?? throw new InvalidArgumentException());
		};
		if (is_string($roles) || $roles instanceof SchoolRoles || is_int($roles))
			$roleNames = [$toName($roles)];
		elseif ($roles instanceof Collection)
			$roleNames = $roles->map($toName)->toArray();
		else
			$roleNames = array_map($toName, $roles);
		return array_merge($this->getIntrinsicFields(), $this->getDynamicFields($roleNames));
	}

	public function isValid(string $key): bool
	{
		return array_key_exists($key, $this->all());
	}

}
