<?php

namespace App\Casts\Utilities;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class AsJsonData implements CastsAttributes
{
	/**
	 * Cast the given value.
	 *
	 * @param array<string, mixed> $attributes
	 */
	public function get(Model $model, string $key, mixed $value, array $attributes): mixed
	{
		if($value == null)
			return new stdClass();
		$json = json_decode($value);
		if(!$json)
			return new stdClass();
		return $json;
	}
	
	/**
	 * Prepare the given value for storage.
	 *
	 * @param array<string, mixed> $attributes
	 */
	public function set(Model $model, string $key, mixed $value, array $attributes): mixed
	{
		return [$key => json_encode($value)];
	}
}
