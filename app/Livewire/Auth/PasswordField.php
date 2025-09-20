<?php

namespace App\Livewire\Auth;

use App\Classes\Settings\AuthSettings;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PasswordField extends Component
{
	public AuthSettings $authSettings;
	#[Validate]
	#[Modelable]
	public string $password = '';
	public bool $clearPassword = false;
	public bool $showClearPassword = true;
	public bool $showGeneratePassword = false;
	public string $id = 'password';
	public string $name = 'password';
	public bool $required = false;
	
	public string $prependText = '';
	public bool $validate = true;
	
	public function mount(AuthSettings $authSettings)
	{
		$this->authSettings = $authSettings;
	}
	
	public function generatePassword()
	{
		$this->resetValidation();
		$this->password = Str::password($this->authSettings->min_password_length, true,
			$this->authSettings->numbers, $this->authSettings->symbols, false);
		$this->dispatch('password-field.password-generated', password: $this->password);
	}
	
	public function render()
	{
		return view('livewire.auth.password-field');
	}
	
	#[On('password-field.load-password')]
	public function loadPassword($password)
	{
		$this->password = $password;
	}
	
	protected function rules(): array
	{
		if(!$this->validate)
			return ['password' => 'nullable'];
		return
			[
				'password' =>
					[
						$this->required ? 'required' : 'nullable',
						Password::defaults(),
					],
			];
	}
}
