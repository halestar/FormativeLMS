<?php

namespace App\Casts\Learning;

use App\Classes\Learning\UrlResource;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class AsUrlResources implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
	    $data = json_decode($value, true);
		if(!$data || !is_array($data))
			return [];
		return array_map(fn($link) => UrlResource::hydrate($link), $data);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
	    if(!$value) return json_encode([]);
	    $links = [];
	    foreach($value as $link)
	    {
		    if(!$link instanceof UrlResource)
			    return [];
		    $links[] = $link->toArray();
	    }
	    return json_encode($links);
    }
}
