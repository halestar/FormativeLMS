<?php

namespace App\Traits;

trait HasDifferentInstance
{
	public function newFromBuilder($attributes = [], $connection = null)
	{
		if($attributes instanceof \stdClass)
			$attributes = json_decode(json_encode($attributes), true);
		if($attributes['className'] == static::class)
			return parent::newFromBuilder($attributes, $connection);
		return (new $attributes['className'])->newFromBuilder($attributes, $connection);
	}
}
