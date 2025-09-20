<?php

namespace App\Livewire\Auth;

use App\Classes\Settings\AuthSettings;
use App\Models\People\Person;
use Illuminate\Support\Facades\Log;
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
	public bool $mustChangePassword = false;
	
	public function mount(Person $person, AuthSettings $authSettings)
	{
		$this->authSettings = $authSettings;
		$this->person = $person;
		$this->mustChangePassword = $person->authConnection->mustChangePassword();
		Log::info("Must change password: " . $this->mustChangePassword);
	}
	
	#[On('password-field.password-generated')]
	public function passwordGenerated($password)
	{
		$this->confirmPassword = $password;
	}
	
	public function resetPassword()
	{
		$connection = $this->person->authConnection;
		if($this->authFirst && !$connection->verifyPassword($this->currentPassword))
		{
			$this->addError('currentPassword', __('errors.auth.password'));
			return;
		}
		$connection->setPassword($this->newPassword);
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
