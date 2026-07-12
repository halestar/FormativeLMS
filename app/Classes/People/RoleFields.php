<?php

namespace App\Classes\People;

use App\Models\People\Person;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Cache;
use JsonSerializable;

class RoleFields implements Arrayable, JsonSerializable
{
	protected array $fields = [];
	protected array $fieldNames = [];
	protected bool $isDirty = false;

	public function __construct(protected Person $person)
	{
		$fields = Cache::tags(
			[
				'people',
				'person-' . $this->person->id,
				'role-fields-' . $this->person->id,
			])->rememberForever('role-fields-' . $this->person->id, function ()
		{
			$roleFields = [];
			$fieldNames = [];
			foreach ($this->person->schoolRoles as $role)
			{
				foreach ($role->fields as $field)
				{
					$roleFields[$field->fieldId] = $field->fieldValue;
					$fieldNames[$field->fieldId] = $field->fieldName;
				}
			}
			return
				[
					'fields' => $roleFields,
					'field_names' => $fieldNames,
				];
		});

		$this->fields = $fields['fields'];
		$this->fieldNames = $fields['field_names'];
	}

	public function __get(string $key): mixed
	{
		if (isset($this->fields[$key]))
			return $this->prettyValue($this->fields[$key]);
		return null;
	}

	public function __set(string $name, mixed $value): void
	{
		if (in_array($name, array_keys($this->fields)) && $this->fields[$name] !== $value)
		{
			$this->fields[$name] = $value;
			$this->isDirty = true;
		}
	}

	public function isDirty(): bool
	{
		return $this->isDirty;
	}

	protected function prettyValue($val)
	{
		if (!$val)
			return "";
		if (is_array($val))
			return implode(", ", $val);
		return (string)$val;
	}

	public function availableFields(): array
	{
		return $this->fieldNames;
	}

	public function toArray()
	{
		return
			[
				'fields' => $this->fields,
				'field_names' => $this->fieldNames,
				'person_id' => $this->person->id,
			];
	}

	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}

	public static function hydrate(array $data): self
	{
		return new self(Person::find($data['person_id']));
	}

	public function save()
	{
		$syncData = [];
		foreach ($this->person->schoolRoles as $role)
		{
			$fieldValues = [];
			//Each Role has a list of its own fields.
			foreach ($role->fields as $field)
				$fieldValues[$field->fieldId] = $this->fields[$field->fieldId] ?? "";
			$syncData[$role->id] = ['field_values' => $fieldValues];
		}
		$this->person->schoolRoles()->syncWithoutDetaching($syncData);
		//clear the cache
		Cache::tags('role-fields-' . $this->person->id)->flush();
	}
}
