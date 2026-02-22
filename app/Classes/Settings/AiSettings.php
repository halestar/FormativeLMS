<?php

namespace App\Classes\Settings;

use App\Models\Ai\Llm;
use App\Models\Integrations\IntegrationConnection;
use App\Models\Utilities\SystemSetting;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AiSettings extends SystemSetting
{
    protected static string $settingKey = 'ai';

    protected static function defaultValue(): array
    {
        return
            [
                'allow_global_ai' => false,
                'allow_user_ai' => true,
                'capture_ai_queries' => false,
                'allow_prompt_editing' => false,
                'default_system_connection' => null,
                'default_model' => null,
            ];
    }

    public function allowGlobalAi(): Attribute
    {
        return $this->basicProperty('allow_global_ai');
    }

    public function allowUserAi(): Attribute
    {
        return $this->basicProperty('allow_user_ai');
    }

    public function captureAiQueries(): Attribute
    {
        return $this->basicProperty('capture_ai_queries');
    }

    public function allowPromptEditing(): Attribute
    {
        return $this->basicProperty('allow_prompt_editing');
    }

    public function defaultModel(): Attribute
    {
	    return Attribute::make(
		    get: function (mixed $value, array $attributes) {
			    $llmId = $this->getValue($attributes['value'], 'default_model', null);
			    if (! $llmId)
				    return null;

			    return Llm::find($llmId);
		    },
		    set: function (?Llm $value, array $attributes) {
			    return $this->updateValue($attributes['value'], 'default_model', $value?->id);
		    },
	    );
    }

    public function defaultSystemConnection(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $connectionId = $this->getValue($attributes['value'], 'default_system_connection');
                if(!$connectionId)
                    return null;

                return IntegrationConnection::find($connectionId);
            },
            set: function (?IntegrationConnection $value, array $attributes) {
                return $this->updateValue($attributes['value'], 'default_system_connection', $value?->id);
            },
        );
    }
}
