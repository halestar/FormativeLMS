<?php

namespace App\Models\Integrations\Connections;

use App\Classes\AI\ProviderOption;
use App\Interfaces\AiPromptable;
use App\Interfaces\Fileable;
use App\Interfaces\IntegrationConnectionInterface;
use App\Models\Ai\AiPrompt;
use App\Models\Ai\Llm;
use App\Models\Integrations\IntegrationConnection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Tool;
use Prism\Prism\ValueObjects\Media\Audio;
use Prism\Prism\ValueObjects\Media\Document;
use Prism\Prism\ValueObjects\Media\Image;
use Prism\Prism\ValueObjects\Media\Video;
use Prism\Relay\Facades\Relay;

abstract class AiConnection extends IntegrationConnection implements IntegrationConnectionInterface
{
	protected function logAiCall(string $aiModel, AiPrompt $prompt, $response)
	{
		Log::channel('ai-calls')->info("************************************************** NEW AI CALL **************************************************");
		Log::channel('ai-calls')->info("Called Prompt: " . $prompt->className . " (id: " . $prompt->id . ") with property: " . $prompt->property . " and model {$aiModel}");
		Log::channel('ai-calls')->info("Tools: " .
		                               print_r(array_map(fn(Tool $tool) => ("Name: " . $tool->name() .
		                                                                    " Description: " . $tool->description()),
			                               Relay::tools('local')), true));

		// Access all tool calls across all steps
		Log::channel('ai-calls')->info("Tool Results: " . count($response->toolCalls));
		foreach ($response->toolCalls as $toolCall)
		{
			Log::channel('ai-calls')->info("Called: {$toolCall->name}");
			Log::channel('ai-calls')->info("Arguments: " . json_encode($toolCall->arguments()));
		}
		// Access tool results
		Log::channel('ai-calls')->info("Tool Results: " . count($response->toolResults));
		foreach ($response->toolResults as $result)
		{
			Log::channel('ai-calls')->info("Tool: " . $result->toolName);
			Log::channel('ai-calls')->info("Result: ". $result->result);
		}
		// Inspect individual steps
		foreach ($response->steps as $step)
		{
			Log::channel('ai-calls')->info("Step finish reason: {$step->finishReason->name}");
			if($step->toolCalls)
				Log::channel('ai-calls')->info("Tools called: " . count($step->toolCalls));

			if ($prompt->structured && $step->structured)
				Log::channel('ai-calls')->info("Contains structured data");
		}
		if($prompt->structured)
			Log::channel('ai-calls')->info("Results: " . print_r($response->structured, true));
		else
			Log::channel('ai-calls')->info("Results: " . $response->text);
		Log::channel('ai-calls')->info("************************************************** END AI CALL **************************************************");
	}

    /**
     * This function is a helper function if you decide to use Prism. Since Prism allows the transfer of files
     * to the AI using their own media formats, this function extracts the relevant files from a Fileable object.
     *
     * @param  Fileable  $fileable  The fileable object to extract files from.
     * @return array An array of media files.
     */
    protected function extractFiles(Fileable $fileable): array
    {
        $files = [];
        foreach ($fileable->workFiles as $file) {
            if ($file->isImage()) {
                $files[] = Image::fromUrl($file->url);
            } elseif ($file->isVideo()) {
                $files[] = Video::fromUrl($file->url);
            } elseif ($file->isAudio()) {
                $files[] = Audio::fromUrl($file->url);
            } elseif ($file->isDocument()) {
                $files[] = Document::fromUrl($file->url);
            }

        }

        return $files;
    }

	public function llms(): HasMany
	{
		return $this->hasMany(Llm::class, 'connection_id');
	}

    /**
     * This function will register all the available LLMS (and their options) by entering them as Llm::class objects.
     * The integrator is responsible for ensuring that the LLMs are correctly registered and that the options are
     * properly configured for each LLM. These LLMs will be selectable when running prompts in the system.
     * @param bool $reset If true this will reset all the options in the LLM
     */
    abstract public function refreshLlms(bool $reset = false): void;

    /**
     * This function will execute a prompt on the AI and return a result. The prompt is provided as an instance of
     * of AiPrompt, which contains all the parameters that FabLMS allows the user to control. The rest of the parameters
     * or any changes to the prompt should be made in this function, then sent to the AI.
     * @param Llm $aiModel The model that you want to execute.
     * @param AiPrompt $prompt The prompt to execute, also where the results will go
     * @param AiPromptable $target The object that the prompt is being executed on.
     */
    abstract public function executePrompt(Llm $aiModel, AiPrompt $prompt, AiPromptable $target): void;

	/**
	 * This function will validate a single ProviderOption for an LLM in this connection. It will return true if the option
	 * and the value provided for the option is valid, false if the value is invalid OR this is an invalid option
	 * for this LLM.
	 * @param Llm $llm The LLM whose parameter you're trying to validate.
	 * @param ProviderOption $option The option you're trying to validate.
	 * @return bool True if the option and value are valid, false otherwise.
	 */
	abstract public function validProviderOption(Llm $llm, ProviderOption $option): bool;
}
