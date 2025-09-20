<?php

namespace App\Livewire\Auth;

use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\IntegrationService;
use App\Models\People\Person;
use Livewire\Component;

class UserAuthManager extends Component
{
	public Person $person;
	public array $authServices;
	public string $authClass;
	public bool $changingAuth = false;
	public bool $changingPasswd = false;
	public string $newPassword = '';
	public bool $mustChangePassword = true;
	public bool $passwordChanged = false;
	public bool $validate = true;
	public bool $isLocked;
	public int $newServiceId;

	public function mount(Person $person)
	{
		$this->person = $person;
		$services = IntegrationService::where('service_type', IntegratorServiceTypes::AUTHENTICATION)->enabled()->get();
		$this->authServices = [];
		foreach($services as $service)
		{
			if($service->ableToConnect($person))
				$this->authServices[] = $service;
		}
		$this->newServiceId = $this->person->authConnection?->service_id ?? $this->authServices[0]?->id ?? 0;
		$this->isLocked = $this->person->authConnection?->isLocked() ?? false;
	}

	public function lockUser()
	{
		$this->person->authConnection?->lockUser();
		$this->isLocked = true;
	}

	public function unlockUser()
	{
		$this->person->authConnection?->lockUser(false);
		$this->isLocked = false;
	}

	public function resetAuth()
	{
		$this->person->authConnection()->disassociate();
		$this->person->save();
		$this->isLocked = false;
	}

	public function changeAuth()
	{
		$this->changingAuth = true;
	}

	public function cancelChangeAuth()
	{
		$this->changingAuth = false;
	}

	public function applyChangeAuth()
	{
		$newService = IntegrationService::find($this->newServiceId);
		$this->person->authConnection()->associate($newService->connect($this->person));
		$this->person->save();
		$this->changingAuth = false;
		$this->isLocked = false;
	}

	public function changePassword()
	{
		$this->changingPasswd = true;
		$this->newPassword = '';
		$this->mustChangePassword = true;
	}

	public function cancelChangePassword()
	{
		$this->changingPasswd = false;
		$this->mustChangePassword = true;
	}

	public function resetPassword()
	{
		$connection = $this->person->authConnection;
		if($connection)
		{
			$connection->setPassword($this->newPassword);
			if($connection->canSetMustChangePassword())
				$connection->setMustChangePassword($this->mustChangePassword);
			$this->changingPasswd = false;
			$this->newPassword = '';
			$this->mustChangePassword = true;
			$this->passwordChanged = true;
			$this->dispatch('user-auth-manager.password-changed');
		}
	}

    public function render()
    {
	    $passwordWasChanged = false;
	    if($this->passwordChanged)
	    {
		    $this->passwordChanged = false;
		    $passwordWasChanged = true;
	    }
	    return view('livewire.auth.user-auth-manager')->with(['passwordWasChanged' => $passwordWasChanged]);
    }
}
