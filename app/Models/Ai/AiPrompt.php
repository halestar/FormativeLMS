<?php

namespace App\Models\Ai;

use App\Enums\WorkStoragesInstances;
use App\Interfaces\AiPromptable;
use App\Interfaces\Fileable;
use App\Models\People\Person;
use App\Models\Utilities\WorkFile;
use App\Traits\HasWorkFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;

class AiPrompt extends Model implements Fileable
{
	use HasWorkFiles;
	
	public $timestamps = true;
	public $incrementing = true;
	protected $table = "ai_prompts";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'person_id',
			'className',
			'property',
			'prompt',
			'system_prompt',
			'structured',
			'temperature',
		];
	
	
	public function getWorkStorageKey(): WorkStoragesInstances
	{
		return WorkStoragesInstances::AiWork;
	}
	
	public function shouldBePublic(): bool
	{
		return false;
	}
	
	public function isDefaultPrompt(): bool
	{
		return ($this->person_id == null);
	}
	
	protected function casts(): array
	{
		return
			[
				'structured' => 'boolean',
				'temperature' => 'float',
				'last_results' => 'array',
				'last_id' => 'string',
			];
	}
	
	public function resetPrompt(): void
	{
		$this->prompt = ($this->className)::defaultPrompt($this->property);
		$this->system_prompt = ($this->className)::defaultSystemPrompt($this->property);
		$this->temperature = ($this->className)::defaultTemperature($this->property);
		$this->save();
	}
	
	public function renderPrompt(AiPromptable $target): string
	{
		return Blade::render($this->prompt, $target->withTokens());
	}

	public function canAccessFile(Person $person, WorkFile $file): bool
	{
		return true;
	}

	public static function systemPrompt(AiPromptable $model, string $property): AiPrompt
	{
		//try to find it in the DB
		$prompt = AiPrompt::where('className', $model::class)->where('property', $property)
			->whereNull('person_id')->first();
		if($prompt)
			return $prompt;
		//else, we create one based on the defaults
		return AiPrompt::create(
		[
			'person_id' => null,
			'className' => $model::class,
			'property' => $property,
			'prompt' => $model::defaultSystemPrompt($property),
			'system_prompt' => $model::defaultSystemPrompt($property),
			'structured' => $model::isStructured($property),
			'temperature' => $model::defaultTemperature($property),
		]);
	}

	public static function userPrompt(AiPromptable $model, string $property, Person $person): AiPrompt
	{
		$prompt = AiPrompt::where('className', $model::class)->where('property', $property)
			->where('person_id', $person->id)->first();
		if($prompt)
			return $prompt;
		//else, we create one based on the default prompt.
		return self::systemPrompt($model, $property)->replicate(['person_id' => $person->id]);
	}

	public function propertyName(): string
	{
		return (($this->className)::availableProperties()[$this->property] ?? $this->property);
	}
}
