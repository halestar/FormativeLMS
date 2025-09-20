<?php

namespace App\Livewire\Auth;

use App\Classes\Auth\AuthenticationDesignation;
use App\Classes\Settings\AuthSettings;
use App\Models\Utilities\SchoolRoles;
use Livewire\Component;

class AuthenticationPriorityManager extends Component
{
	public AuthSettings $authSettings;
	public array $priorities = [];
	public bool $changed = false;
	public bool $editing = false;

	public function mount(AuthSettings $authSettings)
	{
		$this->authSettings = $authSettings;
		$this->priorities = $this->authSettings->priorities;
	}

	public function addPriority()
	{
		$this->priorities[] = AuthenticationDesignation::makeDefaultDesignation(count($this->priorities));
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

	public function updateAuthentication($priority, $service_ids)
	{
		$this->priorities[$priority]->updateServices($service_ids);
		$this->changed = true;
	}

	public function addRoleToPriority(int $priority, SchoolRoles $role)
	{
		if(!in_array($role->name, $this->priorities[$priority]->roles))
			$this->priorities[$priority]->roles[$role->id] = $role->name;
		$this->changed = true;
	}

	public function removeRoleFromPriority(int $priority, SchoolRoles $role)
	{
		$this->priorities[$priority]->roles = array_filter($this->priorities[$priority]->roles, fn($r) => $r != $role->name);
		$this->changed = true;
	}

	public function reorderPriorities($models)
	{
		$pris = [];
		//copy the default
		$pris[0] = $this->priorities[0];
		foreach ($models as $model)
		{
			$pris[$model['order']] = $this->priorities[$model['value']];
			$pris[$model['order']]->priority = $model['order'];
		}
		$this->priorities = $pris;
		$this->changed = true;
	}

	public function applyChanges()
	{
		$this->changed = false;
		$this->authSettings->priorities = $this->priorities;
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
