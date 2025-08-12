<?php

namespace App\Traits;

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
}
