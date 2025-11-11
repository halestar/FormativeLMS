<?php

namespace App\Models\Ai;

use App\Enums\WorkStoragesInstances;
use App\Interfaces\AiPromptable;
use App\Interfaces\Fileable;
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
			'prompt',
			'system_prompt',
			'structured',
			'temperature',
			'last_results',
			'last_id',
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
}
