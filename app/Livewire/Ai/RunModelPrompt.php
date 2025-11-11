<?php

namespace App\Livewire\Ai;

use App\Interfaces\AiPromptable;
use App\Models\Ai\AiPrompt;
use App\Models\People\Person;
use Illuminate\Support\Collection;
use Livewire\Component;

class RunModelPrompt extends Component
{
	public string $classes = "";
	public Person $person;
	public Collection $aiConnections;
	public bool $buttonMode = true;
	public bool $runMode = false;
	public AiPromptable $model;
	public string $property;
	public AiPrompt $prompt;
	public string $selectedAiId;
	public array $Llms = [];
	public string $selectedLlm;
	public bool $resultsMode = false;
	public ?string $results = null;
	public string $propertyName;
	
	public function mount(AiPromptable $model, string $property)
	{
		$this->person = auth()->user();
		$this->aiConnections = $this->person->aiAccess();
		$this->selectedAiId = $this->aiConnections->first()->id;
		$this->Llms = $this->aiConnections->first()
		                                  ->getLlms();
		$this->selectedLlm = $this->Llms[0];
		$this->model = $model;
		$this->property = $property;
		$this->propertyName = $model::availableProperties()[$property];
		$this->prompt = $model::getUserPrompt($property, $this->person);

		//are we in the correct model?
		if($this->prompt->last_id != (string)$this->model->getKey())
		{
			//in this case, save the id and clear the results.
			$this->prompt->last_results = null;
			$this->prompt->last_id = (string)$this->model->getKey();
			$this->prompt->save();
		}
		elseif ($this->prompt->last_results)
		{
			$this->results = $this->model->fillMockup($this->prompt);
			$this->setMode('resultsMode');
		}

	}
	
	public function setMode(string $mode)
	{
		$this->buttonMode = false;
		$this->runMode = false;
		$this->resultsMode = false;
		$this->$mode = true;
	}
	
	public function showPromptResults()
	{
		$this->runPrompt();
		$this->results = $this->model->fillMockup($this->prompt);
		$this->setMode('resultsMode');
	}
	
	private function runPrompt()
	{
		$selectedConnection = $this->aiConnections->where('id', $this->selectedAiId)
		                                          ->first();
		$selectedConnection->executePrompt($this->selectedLlm,
			$this->prompt, $this->model);
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
		$this->setMode('buttonMode');
	}
	
	public function render()
	{
		return view('livewire.ai.run-model-prompt');
	}
}
