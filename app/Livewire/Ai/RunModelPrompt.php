<?php

namespace App\Livewire\Ai;

use App\Classes\Integrators\IntegrationsManager;
use App\Classes\Settings\AiSettings;
use App\Enums\IntegratorServiceTypes;
use App\Interfaces\AiPromptable;
use App\Models\Ai\AiPrompt;
use App\Models\Ai\AiUserQuery;
use App\Models\People\Person;
use Illuminate\Support\Collection;
use Livewire\Attributes\Locked;
use Livewire\Component;

class RunModelPrompt extends Component
{
	//style and position properties
    public string $classes = '';
	public string $style = '';
	public string $btnClasses = 'btn btn-light';
    public ?string $teleportTo = null;

	public AiPromptable $model;
	public string $property;
	public string $propertyName;
	public Person $person;
	public Collection $llms;
	public ?int $selectedLlmId = null;

    public bool $runMode = false;
	public bool $resultsMode = false;
	#[Locked]
	public bool $canEditDefault = false;
	#[Locked]
	public bool $canEditPersonal = false;

    public AiPrompt $prompt;
	public string $renderedPrompt = "";
    public ?string $results = null;

    public function mount(AiPromptable $model, string $property, IntegrationsManager $manager, AiSettings $aiSettings)
    {
		$this->model = $model;
		$this->property = $property;
        $this->person = auth()->user();
		$this->llms = $manager->availableLlms($this->person);
		$this->selectedLlmId = $manager->defaultLlm($this->person)?->id;
	    $this->propertyName = $model::availableProperties()[$property];

		$this->canEditDefault = $this->person->can('system.ai');
		$this->canEditPersonal = $aiSettings->allow_prompt_editing;

		//we either use the user prompt if the system allows users to edit their prompt, or the system prompt.
	    if($this->canEditPersonal)
			$this->prompt = AiPrompt::userPrompt($model, $property, $this->person);
		else
			$this->prompt = AiPrompt::systemPrompt($model, $property);

        if ($this->prompt->last_id != (string) $this->model->getKey())
		{
            // in this case, save the id and clear the results.
            $this->prompt->last_results = null;
            $this->prompt->last_id = (string) $this->model->getKey();
            $this->prompt->save();
        }
		elseif ($this->prompt->last_results)
		{
            $this->results = $this->model->fillMockup($this->prompt);
            $this->resultsMode = true;
        }

    }

    public function showPromptResults()
    {
        $this->runPrompt();
        $this->results = $this->model->fillMockup($this->prompt);
        $this->runMode = false;
        $this->resultsMode = true;
    }

    private function runPrompt()
    {
        $selectedLlm = $this->llms->where('id', $this->selectedLlmId)->first();
		if($selectedLlm)
		{
			$aiSettings = app()->make(AiSettings::class);
			if($aiSettings->capture_ai_queries)
				AiUserQuery::logQuery($this->person, $selectedLlm, $this->prompt, $this->model);
			$selectedLlm->provider->executePrompt($selectedLlm, $this->prompt, $this->model);
		}
        $this->prompt->refresh();
    }

    public function executePrompt()
    {
        $this->runPrompt();
        $this->saveModel();
    }

    public function saveModel()
    {
        $this->model->aiFill($this->prompt);
        $this->model->save();
        $this->prompt->last_results = null;
        $this->prompt->save();
        $this->js('window.location.reload()');
    }

    public function discard()
    {
        $this->prompt->last_results = null;
        $this->prompt->save();
        $this->runMode = false;
        $this->resultsMode = false;
    }

    public function render()
    {
        return view('livewire.ai.run-model-prompt');
    }
}
