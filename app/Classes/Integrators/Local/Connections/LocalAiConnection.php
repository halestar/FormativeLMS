<?php

namespace App\Classes\Integrators\Local\Connections;

use App\Casts\Ai\ProviderOptions;
use App\Classes\AI\ProviderOption;
use App\Enums\BasicDataInput;
use App\Interfaces\AiPromptable;
use App\Interfaces\Fileable;
use App\Models\Ai\AiPrompt;
use App\Models\Ai\Llm;
use App\Models\Integrations\Connections\AiConnection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Relay\Facades\Relay;

class LocalAiConnection extends AiConnection
{
    public function getLlms(): array
    {
        try
        {
            $llms = json_decode(Http::get($this->data->endpoint . "/api/tags")->body(), true);
        }
		catch(\Exception $e)
		{
			Log::error("Failed to fetch Ollama LLMs: {$e->getMessage()}\nTrace: {$e->getTrace()}");
            return [];
        }
        if(!isset($llms['models'])) {
            return [];
        }

        return $llms['models'];
    }

	protected function defaultProviderOptions(): ProviderOptions
	{
		$options = new ProviderOptions;
		$num_ctx = ProviderOption::create(
			[
				'field' => 'num_ctx',
				'title' => __('ai.fields.num_ctx.title'),
				'description' => __('ai.fields.num_ctx.description'),
				'type' => BasicDataInput::NUMBER,
				'value' => 2048,
				'choices' => [],
			]);
		$options->addOption($num_ctx);
		$repeat_last_n = ProviderOption::create(
			[
				'field' => 'repeat_last_n',
				'title' => __('ai.fields.repeat_last_n.title'),
				'description' => __('ai.fields.repeat_last_n.description'),
				'type' => BasicDataInput::NUMBER,
				'value' => 64,
				'choices' => [],
			]);
		$options->addOption($repeat_last_n);
		$repeat_penalty = ProviderOption::create(
			[
				'field' => 'repeat_penalty',
				'title' => __('ai.fields.repeat_penalty.title'),
				'description' => __('ai.fields.repeat_penalty.description'),
				'type' => BasicDataInput::FLOAT,
				'value' => 1.1,
				'choices' => [],
			]);
		$options->addOption($repeat_penalty);
		$seed = ProviderOption::create(
			[
				'field' => 'seed',
				'title' => __('ai.fields.seed.title'),
				'description' => __('ai.fields.seed.description'),
				'type' => BasicDataInput::NUMBER,
				'value' => 0,
				'choices' => [],
			]);
		$options->addOption($seed);
		$stop = ProviderOption::create(
			[
				'field' => 'stop',
				'title' => __('ai.fields.stop.title'),
				'description' => __('ai.fields.stop.description'),
				'type' => BasicDataInput::TEXT,
				'value' => '',
				'choices' => [],
			]);
		$options->addOption($stop);
		$num_predict = ProviderOption::create(
			[
				'field' => 'num_predict',
				'title' => __('ai.fields.num_predict.title'),
				'description' => __('ai.fields.num_predict.description'),
				'type' => BasicDataInput::NUMBER,
				'value' => -1,
				'choices' => [],
			]);
		$options->addOption($num_predict);
		$top_k = ProviderOption::create(
			[
				'field' => 'top_k',
				'title' => __('ai.fields.top_k.title'),
				'description' => __('ai.fields.top_k.description'),
				'type' => BasicDataInput::NUMBER,
				'value' => 40,
				'choices' => [],
			]);
		$options->addOption($top_k);
		$top_p = ProviderOption::create(
			[
				'field' => 'top_p',
				'title' => __('ai.fields.top_p.title'),
				'description' => __('ai.fields.top_p.description'),
				'type' => BasicDataInput::FLOAT,
				'value' => 0.9,
				'choices' => [],
			]);
		$options->addOption($top_p);
		$min_p = ProviderOption::create(
			[
				'field' => 'min_p',
				'title' => __('ai.fields.min_p.title'),
				'description' => __('ai.fields.min_p.description'),
				'type' => BasicDataInput::FLOAT,
				'value' => 0,
				'choices' => [],
			]);
		$options->addOption($min_p);
		return $options;
	}

	public function refreshLlms(bool $reset = false): void
	{
		$models = $this->getLlms();
		$idx = 0;
		$providerOptions = $this->defaultProviderOptions();
		$ids = [];
		foreach($models as $model)
		{
			$llm = Llm::where('connection_id', $this->id)->where('model_id', $model['model'])->first();
			if(!$llm)
			{
				$llm = new Llm();
				$llm->model_id = $model['model'];
				$llm->connection_id = $this->id;
				$llm->name = $model['name'];
				$llm->hide = false;
				$llm->order = $idx;
				$llm->provider_options = $providerOptions;
				$llm->save();
			}
			elseif($reset)
			{
				$llm->name = $model['name'];
				$llm->hide = false;
				$llm->order = $idx;
				$llm->provider_options = $providerOptions;
				$llm->save();
			}
			$ids[] = $llm->id;
			$idx++;
		}
		Llm::where('connection_id', $this->id)->whereNotIn('id', $ids)->delete();
	}

    public function executePrompt(Llm $aiModel, AiPrompt $prompt, AiPromptable $target): void
    {

        $files = $this->extractFiles($prompt);
        if ($target instanceof Fileable) {
            $files += $this->extractFiles($target);
        }

        if ($prompt->structured)
		{
            $response = Prism::structured()
                ->using(Provider::Ollama, $aiModel->model_id,
                    [
                        'url' => $this->data->endpoint,
                    ])
	            ->withSchema($target->getSchema($prompt->property))
                ->withProviderOptions(
                    [
                        'temperature' => $prompt->temperature,
                    ])
	            ->withClientOptions(['timeout' => 240])
                ->withSystemPrompt($prompt->system_prompt)
                ->withPrompt($prompt->renderPrompt($target), $files)
                ->withTools(Relay::tools('local'))
                ->asStructured();
			$prompt->last_results = $response->structured;
        }
		else
		{
            $response = Prism::text()
                ->using(Provider::Ollama, $aiModel->model_id,
                    [
                        'url' => $this->data->endpoint,
                    ])
                ->withProviderOptions(
                    [
                        'temperature' => $prompt->temperature,
                    ])
                ->withSystemPrompt($prompt->system_prompt)
                ->withPrompt($prompt->renderPrompt($target), $files)
                ->withTools(Relay::tools('local'))
                ->asText();
			$prompt->last_results = $response->text;
        }
	    $prompt->save();
	    $this->logAiCall($aiModel, $prompt, $response);
    }

    /**
     * {@inheritDoc}
     */
    public static function getInstanceDefault(): array
    {
        return
        [
            'endpoint' => null,
            'verified' => false,
        ];
    }

    public static function getSystemInstanceDefault(): array
    {
        return
            [
                'endpoint' => null,
                'verified' => false,
            ];
    }

	public function validProviderOption(Llm $llm, ProviderOption $option): bool
	{
		return true;
	}
}
