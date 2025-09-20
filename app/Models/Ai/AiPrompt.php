<?php

namespace App\Models\Ai;

use App\Enums\WorkStoragesInstances;
use App\Interfaces\AiPromptable;
use App\Interfaces\Fileable;
use App\Traits\HasWorkFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AiPrompt extends Model implements Fileable
{
	use HasWorkFiles;
	
	public $timestamps = true;
	public $incrementing = true;
	protected $table = "ai_prompts";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'system_prompt_id',
			'person_id',
			'prompt',
			'structured',
			'temperature',
			'tools',
			'last_results'
		];
	
	/**
	 * Get the promtable model, which will always implement AiPromptable.
	 * @return AiPromptable The object that owns this prompt.
	 */
	public function ai_promptable(): MorphTo
	{
		return $this->morphTo();
	}
	
	public function systemPrompt(): BelongsTo
	{
		return $this->belongsTo(AiSystemPrompt::class, 'system_prompt_id');
	}
	
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
				'tools' => 'array',
				'last_results' => 'array',
			];
	}
}
