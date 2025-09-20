<?php

namespace App\Livewire\Ai;

use App\Models\Ai\AiPrompt;
use App\Models\Ai\AiSystemPrompt;
use Livewire\Component;

class EditModelPrompt extends Component
{
	public array $breadcrumb;
	public AiPrompt $aiPrompt;
	public AiSystemPrompt $aiSystemPrompt;
	public string $reloadKey;
	public string $prompt;
	public float $temperature;
	public string $promptType;
	
	public function mount(AiPrompt $aiPrompt)
	{
		//should the person be here?
		if($aiPrompt->isDefaultPrompt() && !auth()->user()->can('system.ai'))
			abort(403);
		elseif(!$aiPrompt->isDefaultPrompt() && auth()->user()->id != $aiPrompt->person_id)
			abort(403);
		$this->aiPrompt = $aiPrompt;
		$this->aiSystemPrompt = $aiPrompt->systemPrompt;
		$this->breadcrumb = $this->aiPrompt->ai_promptable->getBreacrumb();
		$this->breadcrumb[__('ai.prompt.editor')] = '#';
		$this->reloadKey = uniqid();
		$this->prompt = $this->aiPrompt->prompt;
		$this->temperature = $this->aiPrompt->temperature;
		$this->promptType = 'prompt';
	}
	
	public function setPromptType()
	{
		$this->promptType = ($this->promptType == 'prompt' || $this->promptType == 'system')? $this->promptType: 'prompt';
		if($this->promptType == 'prompt')
		{
			$this->prompt = $this->aiPrompt->prompt;
			$this->reloadKey = uniqid();
		}
		else
		{
			$this->prompt = $this->aiSystemPrompt->prompt;
			$this->reloadKey = uniqid();
		}
	}
	
	public function revert()
	{
		$this->prompt = $this->aiPrompt->prompt;
		$this->temperature = $this->aiPrompt->temperature;
		$this->reloadKey = uniqid();
	}
	
	public function updatePrompt()
	{
		if($this->promptType == 'prompt')
			$this->aiPrompt->prompt = $this->prompt;
		else
		{
			$this->aiSystemPrompt->prompt = $this->prompt;
			$this->aiSystemPrompt->save();
		}
		$this->aiPrompt->temperature = $this->temperature;
		$this->aiPrompt->save();
		$this->dispatch('saved');
	}
	
	public function resetPrompt()
	{
		if($this->promptType == 'prompt')
		{
			$this->aiPrompt = $this->aiPrompt->ai_promptable->getDefaultPrompt(true);
			$this->prompt = $this->aiPrompt->prompt;
		}
		else
		{
			$this->aiSystemPrompt = $this->aiPrompt->ai_promptable->getDefaultSystemPrompt(true);
			$this->prompt = $this->aiSystemPrompt->prompt;
		}
		$this->reloadKey = uniqid();
	}
	
    public function render()
    {
        return view('livewire.ai.edit-model-prompt')
	        ->extends('layouts.app', ['breadcrumb' => $this->breadcrumb])
	        ->section('content');
    }
}
