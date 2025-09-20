<?php

namespace App\Casts\Utilities\SystemSettings;

use App\Classes\Storage\Work\WorkStorage;
use App\Traits\UsesJsonValue;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class AsWorkStorage implements CastsAttributes
{
	use UsesJsonValue;
	
	/**
	 * Cast the given value.
	 *
	 * @param array<string, mixed> $attributes
	 */
	public function get(Model $model, string $key, mixed $value, array $attributes): mixed
	{
		$storage = $this->getValue($attributes['value'], $key, null);
		if(!$storage)
			return null;
		return WorkStorage::hydrate($storage);
	}
	
	/**
	 * Prepare the given value for storage.
	 *
	 * @param array<string, mixed> $attributes
	 */
	public function set(Model $model, string $key, mixed $value, array $attributes): mixed
	{
		if(!($value instanceof WorkStorage))
			return ['value' => $attributes['value']];
		return $this->updateValue($attributes['value'], $key, $value->toArray());
	}
}
