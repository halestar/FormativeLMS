<?php

namespace App\Models\Ai;

use App\Models\People\Person;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiSystemPrompt extends Model
{
	public $timestamps = true;
	public $incrementing = true;
	protected $table = "ai_system_prompts";
	protected $primaryKey = "id";
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
