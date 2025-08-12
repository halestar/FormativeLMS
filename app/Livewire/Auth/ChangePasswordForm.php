<?php

namespace App\Livewire\Auth;

use App\Classes\Settings\AuthSettings;
use App\Models\People\Person;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\On;
use Livewire\Component;

class ChangePasswordForm extends Component
{
	public AuthSettings $authSettings;
	public Person $person;
	public string $currentPassword = '';
	public string $newPassword = '';
	public string $confirmPassword = '';
	public bool $authFirst = true;
	public bool $passwordChangedSuccessfully = false;

	public function mount(Person $person, AuthSettings $authSettings)
	{
		$this->authSettings = $authSettings;
		$this->person = $person;
	}

	#[On('password-field.password-generated')]
	public function passwordGenerated($password)
	{
		$this->confirmPassword = $password;
	}

	public function resetPassword()
	{
		if($this->authFirst && !$this->person->auth_driver->verifyPassword($this->currentPassword))
		{
			$this->addError('currentPassword', __('errors.auth.password'));
			return;
		}
		$this->person->auth_driver->setPassword($this->newPassword);
		$this->person->auth_driver->setMustChangePassword(false);
		$this->person->save();
		$this->reset('currentPassword', 'newPassword', 'confirmPassword');
		$this->passwordChangedSuccessfully = true;
		$this->dispatch('change-password-form.password-changed');
	}

    public function render()
    {
        return view('livewire.auth.change-password-form');
    }

	protected function rules(): array
	{
		return
			[
				'newPassword' =>
					[
						'required',
						Password::defaults(),
						'confirmed:confirmPassword',
					],
			];
	}
}
