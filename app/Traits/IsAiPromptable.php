<?php

namespace App\Traits;

use App\Models\Ai\AiPrompt;
use App\Models\Ai\AiSystemPrompt;
use App\Models\People\Person;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait IsAiPromptable
{
	protected function getSystemPrompt(): AiSystemPrompt
	{
		return AiSystemPrompt::where('className', static::class)->whereNull('person_id')->first();
	}
    public function ai_prompts(): MorphMany
    {
		return $this->morphMany(AiPrompt::class, 'ai_promptable');
    }
	
	public function hasCustomPrompt(Person $person): bool
	{
		return $this->ai_prompts()->where('person_id', $person->id)->exists();
	}
	
	public function customPrompt(Person $person): AiPrompt
	{
		if($this->hasCustomPrompt($person))
			return $this->ai_prompts()->where('person_id', $person->id)->first();
		//else, we create one based on the default prompt
		$defaultSystemPrompt = $this->getDefaultSystemPrompt();
		$defaultPrompt = $this->getDefaultPrompt();
		$customSystemPrompt = $defaultSystemPrompt->replicate()->fill(['person_id' => $person->id]);
		$customSystemPrompt->save();
		$customPrompt = $defaultPrompt->replicate()->fill(['person_id' => $person->id, 'system_prompt_id' => $customSystemPrompt->id]);
		$customPrompt->save();
		$customPrompt->workFiles()->sync($defaultPrompt->workFiles->pluck('id'));
		return $customPrompt;
	}
}
