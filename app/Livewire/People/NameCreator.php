<?php

namespace App\Livewire\People;

use App\Classes\NameConstructor;
use App\Classes\NameToken;
use App\Classes\Settings\SchoolSettings;
use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use Livewire\Component;

class NameCreator extends Component
{
	public SchoolSettings $settings;
	public SchoolRoles $role;
	
	public array $tokens;
	public Person $samplePerson;
	public string $sampleName;
	
	public function mount(SchoolRoles $role, SchoolSettings $settings)
	{
		$this->settings = $settings;
		$this->role = $role;
		if($this->role->name == SchoolRoles::$STUDENT)
			$nConstructor = $this->settings->studentName;
		elseif($this->role->name == SchoolRoles::$EMPLOYEE)
			$nConstructor = $this->settings->employeeName;
		else
			$nConstructor = $this->settings->parentName;
		$this->tokens = $nConstructor->tokens;
		$this->samplePerson = Person::join('model_has_roles', 'model_has_roles.model_id', '=', 'people.id')
		                            ->where('model_has_roles.role_id', $this->role->id)
		                            ->inRandomOrder()
		                            ->first();
		$this->sampleName = $nConstructor->applyName($this->samplePerson);
	}
	
	public function addBasicToken()
	{
		$token = new NameToken(NameToken::TYPE_BASIC_FIELD);
		$token->basicFieldName = array_key_first(NameToken::basicFields());
		$this->tokens[] = $token;
		$this->updateSampleName();
	}
	
	private function updateSampleName()
	{
		$nConstructor = new NameConstructor($this->tokens);
		$this->sampleName = $nConstructor->applyName($this->samplePerson);
	}
	
	public function updateBasicFieldName(int $idx, string $field)
	{
		$this->tokens[$idx]->basicFieldName = $field;
		$this->updateSampleName();
	}
	
	public function addRoleToken()
	{
		$token = new NameToken(NameToken::TYPE_ROLE_FIELD);
		$token->roleField = $this->role->fields[array_key_first($this->role->fields)];
		$token->roleId = $this->role->id;
		$this->tokens[] = $token;
		$this->updateSampleName();
	}
	
	public function updateRoleField(int $idx, string $fieldId)
	{
		$this->tokens[$idx]->roleField = $this->role->fields[$fieldId];
		$this->updateSampleName();
	}
	
	public function addTextToken()
	{
		$token = new NameToken(NameToken::TYPE_TEXT);
		$token->textContent = "";
		$this->tokens[] = $token;
		$this->updateSampleName();
	}
	
	public function updateText(int $idx, string $text)
	{
		$this->tokens[$idx]->textContent = $text;
		$this->updateSampleName();
	}
	
	public function updateSpaceAfter(int $idx, bool $spaceAfter)
	{
		$this->tokens[$idx]->spaceAfter = $spaceAfter;
		$this->updateSampleName();
	}
	
	public function removeToken(int $idx)
	{
		$tokens = [];
		for($i = 0; $i < count($this->tokens); $i++)
		{
			if($i != $idx)
				$tokens[] = $this->tokens[$i];
		}
		$this->tokens = $tokens;
		$this->updateSampleName();
	}
	
	public function newSamplePerson()
	{
		$this->samplePerson = Person::join('model_has_roles', 'model_has_roles.model_id', '=', 'people.id')
		                            ->where('model_has_roles.role_id', $this->role->id)
		                            ->inRandomOrder()
		                            ->first();
		$this->updateSampleName();
	}
	
	public function saveName()
	{
		$nConstructor = new NameConstructor($this->tokens);
		if($this->role->name == SchoolRoles::$STUDENT)
			$this->settings->studentName = $nConstructor;
		elseif($this->role->name == SchoolRoles::$EMPLOYEE)
			$this->settings->employeeName = $nConstructor;
		else
			$this->settings->parentName = $nConstructor;
		$this->settings->save();
	}
	
	public function resetName()
	{
		if($this->role->name == SchoolRoles::$STUDENT)
			$nConstructor = $this->settings->studentName;
		elseif($this->role->name == SchoolRoles::$EMPLOYEE)
			$nConstructor = $this->settings->employeeName;
		else
			$nConstructor = $this->settings->parentName;
		$this->tokens = $nConstructor->tokens;
		$this->updateSampleName();
	}
	
	public function updateOrder($models)
	{
		$updatedTokens = [];
		foreach($models as $model)
			$updatedTokens[($model['order'] - 1)] = $this->tokens[$model['value']];
		$this->tokens = $updatedTokens;
		$this->updateSampleName();
	}
	
	public function render()
	{
		return view('livewire.people.name-creator');
	}
}
