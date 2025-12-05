<?php

namespace App\Casts\Utilities;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CarbonDate implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $data = json_decode($attributes['value'], true) ?? [];
        return Carbon::parse($data[$key] ?? $model::defaultValue()[$key]);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $data = json_decode($attributes['value'], true);
        $data[$key] = ($value instanceof Carbon)? $value->format('Y-m-d H:i:s'): $value;
        return ['value' => json_encode($data)];
    }
}
