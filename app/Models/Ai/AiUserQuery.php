<?php

namespace App\Models\Ai;

use App\Interfaces\AiPromptable;
use App\Models\Integrations\Connections\AiConnection;
use App\Models\People\Person;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiUserQuery extends Model
{
    protected $table = "ai_user_queries";
    protected $primaryKey = "id";
    public $timestamps = true;
    public $incrementing = true;
    protected $guarded = ['id',];

	public function person(): BelongsTo
	{
		return $this->belongsTo(Person::class, 'person_id');
	}

	public static function logQuery(Person $person, Llm $llm, AiPrompt $prompt, AiPromptable $targetModel): AiUserQuery
	{
		$connection_info = "Using service " . $llm->provider->service->name;
		if($llm->provider->isSystem())
			$connection_info .= " (system)";
		else
			$connection_info .= " (personal)";
		return AiUserQuery::create(
			[
				'person_id' => $person->id,
				'connection_info' => $connection_info,
				'llm' => $llm->name,
				'prompt' => $prompt->renderPrompt($targetModel),
				'system_prompt' => $prompt->system_prompt,
			]);
	}
}
