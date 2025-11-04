<?php

namespace App\Traits;

use App\Models\Ai\AiPrompt;
use App\Models\Ai\AiSystemPrompt;
use App\Models\People\Person;
use Illuminate\Database\Eloquent\Builder;

trait IsAiPromptable
{
	public static function prompts(string $property = null): Builder
	{
		if(!$property)
			return AiPrompt::where('className', static::class);
		return AiPrompt::where('className', static::class)->where('property', $property);
	}
	
	public static function getDefaultPrompt(string $property, bool $overwrite = false): AiPrompt
	{
		$prompt = static::prompts($property)->whereNull('person_id');
		//do we have one?
		if($prompt->exists())
		{
			$defaultPrompt = $prompt->first();
			if(!$overwrite)
				return $prompt->first();
		}
		else
		{
			$defaultPrompt = new AiPrompt();
			$defaultPrompt->person_id = null;
			$defaultPrompt->className = static::class;
			$defaultPrompt->property = $property;
			$defaultPrompt->structured = static::isStructured($property);
			$defaultPrompt->tools = static::defaultTools($property);
		}
		$defaultPrompt->temperature = static::defaultTemperature($property);
		$defaultPrompt->prompt = static::defaultPrompt($property);
		$defaultPrompt->system_prompt = static::defaultSystemPrompt($property);
		$defaultPrompt->save();
		return $defaultPrompt;
	}
	
	public static function hasCustomPrompt(string $property, Person $person): bool
	{
		return static::prompts($property)->where('person_id', $person->id)->exists();
	}
	
	public static function getCustomPrompt(string $property, Person $person): AiPrompt
	{
		if(static::hasCustomPrompt($property, $person))
			return static::prompts($property)
			            ->where('person_id', $person->id)
			            ->first();
		//else, we create one based on the default prompt
		$defaultPrompt = static::getDefaultPrompt($property);
		$customPrompt = $defaultPrompt->replicate()
		                              ->fill(['person_id' => $person->id]);
		$customPrompt->save();
		$customPrompt->workFiles()
		             ->sync($defaultPrompt->workFiles->pluck('id'));
		return $customPrompt;
	}
	
	public static function propertyName(string $property): string
	{
		return (static::availableProperties()[$property] ?? $property);
	}
}
