<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait UsesJsonValue
{
	protected function updateValue(string $values, string $key, mixed $value)
	{
		$data = json_decode($values, true);
		$data[$key] = $value;
		return ['value' => json_encode($data)];
	}

	protected function getValue(string $values, string $key, mixed $default = null)
	{
		$data = json_decode($values, true);
		return $data[$key] ?? $default;
	}
	
	protected function basicProperty($propertyName = null): Attribute
	{
		return Attribute::make
		(
			get: fn(mixed $value, array $attributes) =>
			$this->getValue($attributes['value'], $propertyName, (static::defaultValue())[$propertyName]),
			set: fn(mixed $value, array $attributes) =>
			$this->updateValue($attributes['value'], $propertyName, $value),
		);
	}
	
}
