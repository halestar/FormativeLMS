<?php

namespace App\Livewire\Ai;

use App\Models\Ai\AiPrompt;
use Illuminate\Support\Facades\Blade;
use Livewire\Component;

class EditModelPrompt extends Component
{
	public array $breadcrumb;
	public AiPrompt $aiPrompt;
	public string $className;
	public string $property;
	public string $reloadKey;
	public string $prompt;
	public float $temperature;
	public string $promptType;
	public string $backLink;
	public ?string $preview = null;
	
	public function mount(AiPrompt $aiPrompt)
	{
		//should the person be here?
		if($aiPrompt->isDefaultPrompt() && !auth()
				->user()
				->can('system.ai'))
			abort(403);
		elseif(!$aiPrompt->isDefaultPrompt() && auth()->user()->id != $aiPrompt->person_id)
			abort(403);
		$this->aiPrompt = $aiPrompt;
		$this->className = $aiPrompt->className;
		$this->property = $aiPrompt->property;
		$this->backLink = redirect()->back()->getTargetUrl();
		$this->breadcrumb =
			[
				($this->className)::propertyName($this->property) => $this->backLink,
				__('ai.prompt.editor') => '#',
			];
		$this->reloadKey = uniqid();
		$this->prompt = $this->aiPrompt->prompt;
		$this->temperature = $this->aiPrompt->temperature;
		$this->promptType = 'prompt';
	}
	
	public function setPromptType()
	{
		$this->promptType = ($this->promptType == 'prompt' || $this->promptType == 'system') ? $this->promptType : 'prompt';
		if($this->promptType == 'prompt')
		{
			$this->prompt = $this->aiPrompt->prompt;
			$this->reloadKey = uniqid();
		}
		else
		{
			$this->prompt = $this->aiPrompt->system_prompt;
			$this->reloadKey = uniqid();
		}
	}
	
	public function revert()
	{
		$this->prompt = $this->aiPrompt->prompt;
		$this->temperature = $this->aiPrompt->temperature;
		$this->promptType = 'prompt';
		$this->reloadKey = uniqid();
	}
	
	public function updatePrompt()
	{
		if($this->promptType == 'prompt')
			$this->aiPrompt->prompt = $this->prompt;
		else
			$this->aiPrompt->system_prompt = $this->prompt;
		$this->aiPrompt->temperature = $this->temperature;
		$this->aiPrompt->save();
		$this->dispatch('saved');
	}
	
	public function resetPrompt()
	{
		$this->aiPrompt->resetPrompt();
		$this->prompt = $this->aiPrompt->prompt;
		$this->temperature = $this->aiPrompt->temperature;
		$this->reloadKey = uniqid();
	}
	
	public function previewPrompt()
	{
		//load a random model.
		$model = ($this->className)::inRandomOrder()->first();
		$this->preview = Blade::render($this->prompt, $model->withTokens());
	}
	
	public function render()
	{
		return view('livewire.ai.edit-model-prompt')
			->extends('layouts.app', ['breadcrumb' => $this->breadcrumb])
			->section('content');
	}
}
