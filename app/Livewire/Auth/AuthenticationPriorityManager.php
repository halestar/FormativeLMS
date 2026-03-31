<?php

namespace App\Livewire\Auth;

use App\Classes\Auth\AuthenticationDesignation;
use App\Classes\Settings\AuthSettings;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class AuthenticationPriorityManager extends Component
{
	public AuthSettings $authSettings;
	public array $priorities = [];
	public bool $changed = false;
	public bool $editing = false;

	private function loadPriorities()
	{
		$this->priorities = [];
		foreach($this->authSettings->priorities as $priority)
		{
			$this->priorities[$priority->priority] =
				[
					'roles' => $priority->roles,
					'auth' => $priority->isChoice()? "choose": ($priority->isBlocked()? null: ($priority->services->first()?->id ?? null)),
					'choices' => $priority->isChoice()? $priority->services->mapWithKeys(fn($item) => [$item->id => $item->name]): [],
				];
		}
	}
	
	public function mount(AuthSettings $authSettings)
	{
		$this->authSettings = $authSettings;
		$this->loadPriorities();
	}
	
	public function addPriority()
	{
		$this->priorities[] =
			[
				'roles' => [],
				'auth' => null,
				'choices' => [],
			];
		$this->changed = true;
	}
	
	public function removePriority($priority)
	{
		if($priority == AuthenticationDesignation::DEFAULT_PRIORITY)
			return;
		$pris = [];
		$i = 0;
		foreach($this->priorities as $pri)
		{
			if($pri->priority == $priority)
				continue;
			$pri->priority = $i;
			$pris[] = $pri;
			$i++;
		}
		$this->priorities = $pris;
		$this->changed = true;
	}

	public function reorderPriorities($id, $position)
	{
		$element = $this->priorities[$id];
		unset($this->priorities[$id]);
		$this->priorities = array_values($this->priorities);
		array_splice($this->priorities, $position + 1, 0, [$element]);
		$this->changed = true;
	}
	
	public function applyChanges()
	{
		$this->changed = false;
		$priorities = [];
		foreach($this->priorities as $priorityIdx => $priority)
		{
			$priorities[] = AuthenticationDesignation::hydrate(
				[
					'priority' => $priorityIdx,
					'roles' => $priority['roles'],
					'service_ids' => $priority['auth'] == 'choose'? $priority['choices']: [$priority['auth']],
				]);
		}
		$this->authSettings->priorities = $priorities;
		$this->authSettings->save();
		AuthSettings::applyAuthenticationPriorities();
		$this->editing = false;
	}
	
	public function revertChanges()
	{
		$this->changed = false;
		$this->priorities = $this->authSettings->priorities;
	}
	
	public function render()
	{
		return view('livewire.auth.authentication-priority-manager');
	}
}
