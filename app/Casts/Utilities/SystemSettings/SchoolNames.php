<?php

namespace App\Casts\Utilities\SystemSettings;

use App\Classes\NameConstructor;
use App\Classes\NameToken;
use App\Traits\UsesJsonValue;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class SchoolNames implements CastsAttributes
{
	use UsesJsonValue;
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
	    $tokens = $this->getValue($attributes['value'], $key, []);
	    $nameTokens = [];
	    foreach($tokens as $token)
		    $nameTokens[] = new NameToken($token['type'], $token);
	    return new NameConstructor($nameTokens);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
	    return $this->updateValue($attributes['value'], $key, $value->tokens);
    }
}
