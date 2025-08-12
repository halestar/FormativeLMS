<?php

namespace App\Livewire\Auth;

use App\Models\People\Person;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class UserAuthManager extends Component
{
	public Person $person;
	public ?string $authDriver;
	public string $authClass;
	public bool $changingAuth = false;
	public bool $changingPasswd = false;
	public string $newPassword = '';
	public bool $mustChangePassword = true;
	public bool $passwordChanged = false;
	public bool $validate = true;

	public function mount(Person $person)
	{
		$this->person = $person;
		$this->authDriver = $this->person->auth_driver? $this->person->auth_driver::driverName(): null;
	}

	public function lockUser()
	{
		$this->person->auth_driver->lockUser();
	}

	public function unlockUser()
	{
		$this->person->auth_driver->lockUser(false);
	}

	public function resetAuth()
	{
		$this->person->auth_driver = null;
		$this->person->save();
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
		$this->person->auth_driver = $this->authDriver;
		$this->person->save();
		$this->changingAuth = false;
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
		$authDriver = $this->person->auth_driver;
		if($authDriver) 
		{
			$authDriver->setPassword($this->newPassword);
			if($authDriver->canSetMustChangePassword())
				$authDriver->setMustChangePassword($this->mustChangePassword);
			$this->person->auth_driver = $authDriver->driverName();
			$this->person->save();
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
