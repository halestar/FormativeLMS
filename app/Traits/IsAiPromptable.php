<?php

namespace App\Traits;

use App\Models\Ai\AiPrompt;
use App\Models\Ai\AiSystemPrompt;
use App\Models\People\Person;
use Illuminate\Database\Eloquent\Builder;

trait IsAiPromptable
{
    protected const PROMPT_VIEW_PATH = 'ai.prompts.';
	public static function prompts(string $property = null): Builder
	{
		if(!$property)
			return AiPrompt::where('className', static::class);
		return AiPrompt::where('className', static::class)->where('property', $property);
	}

	
	public static function getUserPrompt(string $property, Person $person): AiPrompt
	{
		$prompt = AiPrompt::where('className', static::class)
            ->where('property', $property)
            ->where('person_id', $person->id)
            ->first();
        if($prompt)
            return $prompt;
        //else, we create one based on the defaults
        $prompt = new AiPrompt();
        $prompt->className = static::class;
        $prompt->property = $property;
        $prompt->person_id = $person->id;
        $prompt->prompt = static::defaultPrompt($property);
        $prompt->system_prompt = static::defaultSystemPrompt($property);
        $prompt->structured = static::isStructured($property);
        $prompt->temperature = static::defaultTemperature($property);
		$prompt->save();
		return $prompt;
	}
	
	public static function propertyName(string $property): string
	{
		return (static::availableProperties()[$property] ?? $property);
	}
}
