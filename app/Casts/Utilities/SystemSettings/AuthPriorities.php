<?php

namespace App\Casts\Utilities\SystemSettings;

use App\Classes\Auth\AuthenticationDesignation;
use App\Traits\UsesJsonValue;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class AuthPriorities implements CastsAttributes
{
	use UsesJsonValue;

    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {
		$pris = $attributes['value'];
		if(is_string($pris))
			$pris = json_decode($pris, true);
	    $pris = $pris['priorities'];
	    if(count($pris) == 0)
		    return [AuthenticationDesignation::makeDefaultDesignation()];
	    $priorities = [];
	    foreach($pris as $v)
		    $priorities[] = AuthenticationDesignation::hydrate($v);
	    return $priorities;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
		if(!is_array($value) || count($value) == 0)
			return ['value' => $attributes['value']];
	    $jsonPriorities = [];
	    foreach($value as $v)
	    {
			if(!($v instanceof AuthenticationDesignation))
				return ['value' => $attributes['value']];
		    $jsonPriorities[] = $v->toArray();
	    }
	    return $this->updateValue($attributes['value'], 'properties', $jsonPriorities);
    }
}
