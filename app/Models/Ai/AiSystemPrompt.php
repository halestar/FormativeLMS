<?php

namespace App\Models\Ai;

use App\Models\People\Person;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiSystemPrompt extends Model
{
	public $timestamps = true;
	protected $table = "ai_system_prompts";
	protected $primaryKey = "id";
	public $incrementing = true;
	protected $fillable =
		[
			'person_id',
			'prompt',
			'className',
		];
	
	public function prompts(): HasMany
	{
		return $this->hasMany(AiPrompt::class, 'system_prompt_id');
	}
	
	public function person(): BelongsTo
	{
		return $this->belongsTo(Person::class, 'person_id');
	}
}
