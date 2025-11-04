<?php

namespace App\Casts\Learning;

use App\Classes\Learning\DemonstrationQuestion;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class AsDemonstrationQuestions implements CastsAttributes
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
		return array_map(fn($question) => DemonstrationQuestion::hydrate($question), $data);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if(!$value) return [];
		$questions = [];
		foreach($value as $question)
		{
			if(!$question instanceof DemonstrationQuestion)
				return [];
			$questions[] = $question->toArray();
		}
		return json_encode($questions);
    }
}
