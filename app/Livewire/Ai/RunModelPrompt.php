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
	public AiPrompt $defaultPrompt;
	public ?AiPrompt $customPrompt = null;
	public string $selectedAiId;
	public array $Llms = [];
	public string $selectedLlm;
	public bool $resultsMode = false;
	public ?string $results = null;
	public string $promptType;
	
	public function mount(AiPromptable $model)
	{
		$this->person = auth()->user();
		$this->aiConnections = $this->person->aiAccess();
		$this->selectedAiId = $this->aiConnections->first()->id;
		$this->Llms = $this->aiConnections->first()->getLlms();
		$this->selectedLlm = $this->Llms[0];
		$this->model = $model;
		$this->defaultPrompt = $this->model->getDefaultPrompt();
		$this->promptType = "default";
		if($model->hasCustomPrompt($this->person))
		{
			$this->customPrompt = $model->customPrompt($this->person);
			$this->promptType = "custom";
		}
		
		//do we have saved results?
		if($this->defaultPrompt->last_results)
		{
			$this->results = $this->model->fillMockup($this->defaultPrompt);
			$this->setMode('resultsMode');
			$this->promptType = "default";
		}
		elseif($this->customPrompt && $this->customPrompt->last_results)
		{
			$this->results = $this->model->fillMockup($this->customPrompt);
			$this->setMode('resultsMode');
			$this->promptType = "custom";
		}
	}
	
	public function setMode(string $mode)
	{
		$this->buttonMode = false;
		$this->runMode = false;
		$this->resultsMode = false;
		$this->$mode = true;
	}
	
	private function runPrompt()
	{
		$selectedConnection = $this->aiConnections->where('id', $this->selectedAiId)->first();
		$selectedConnection->executePrompt($this->selectedLlm, ($this->promptType == "default"? $this->defaultPrompt: $this->customPrompt));
		if($this->promptType == "default")
			$this->defaultPrompt->refresh();
		else
			$this->customPrompt->refresh();
	}
	
	public function showPromptResults()
	{
		$this->runPrompt();
		$this->results = $this->model->fillMockup(($this->promptType == "default"? $this->defaultPrompt: $this->customPrompt));
		$this->setMode('resultsMode');
	}
	
	public function executePrompt()
	{
		$this->runPrompt();
		$this->saveModel();
	}
	
	public function discard()
	{
		$this->results = null;
		if($this->promptType == "default")
		{
			$this->defaultPrompt->last_results = null;
			$this->defaultPrompt->save();
		}
		else
		{
			$this->customPrompt->last_results = null;
			$this->customPrompt->save();
		}
		$this->setMode('buttonMode');
	}
	
	public function saveModel()
	{
		$this->model->aiFill(($this->promptType == "default"? $this->defaultPrompt: $this->customPrompt));
		$this->model->save();
		if($this->promptType == "default")
		{
			$this->defaultPrompt->last_results = null;
			$this->defaultPrompt->save();
		}
		else
		{
			$this->customPrompt->last_results = null;
			$this->customPrompt->save();
		}
		$this->js('window.location.reload()');
	}
	
	public function createCustomPrompt()
	{
		$this->customPrompt = $this->model->customPrompt($this->person);
		$this->redirect(route('ai.prompt.editor', $this->customPrompt));
	}
	
    public function render()
    {
        return view('livewire.ai.run-model-prompt');
    }
}
