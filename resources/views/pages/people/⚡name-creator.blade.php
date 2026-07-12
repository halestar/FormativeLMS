<?php

use App\Classes\People\NameConstructor;
use App\Classes\People\NameToken;
use App\Classes\Settings\SchoolSettings;
use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use App\Traits\FullPageComponent;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
	use FullPageComponent;

	public SchoolSettings $settings;
	public SchoolRoles $role;
	public array $tokens;
	public Person $samplePerson;

	public function mount(SchoolRoles $role, SchoolSettings $settings)
	{
		$this->authorize('has-permission', 'school');
		$this->breadcrumb =
			[
				__('system.menu.school.settings') => route('settings.school'),
				__('system.settings.names.title', ['role' => $role->name]) => '#',
			];
		$this->settings = $settings;
		$this->role = $role;
		if ($this->role->name == SchoolRoles::$STUDENT)
			$nConstructor = $this->settings->studentName;
		elseif ($this->role->name == SchoolRoles::$EMPLOYEE)
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
	}

	#[Computed]
	public function sampleName()
	{
		$nConstructor = new NameConstructor($this->tokens);
		return $nConstructor->applyName($this->samplePerson);
	}

	public function updateBasicFieldName(int $idx, string $field)
	{
		$this->tokens[$idx]->basicFieldName = $field;
	}

	public function addRoleToken()
	{
		$token = new NameToken(NameToken::TYPE_ROLE_FIELD);
		$token->roleField = $this->role->fields[array_key_first($this->role->fields)];
		$token->roleId = $this->role->id;
		$this->tokens[] = $token;
	}

	public function updateRoleField(int $idx, string $fieldId)
	{
		$this->tokens[$idx]->roleField = $this->role->fields[$fieldId];
	}

	public function addTextToken()
	{
		$token = new NameToken(NameToken::TYPE_TEXT);
		$token->textContent = "";
		$this->tokens[] = $token;
	}

	public function updateText(int $idx, string $text)
	{
		$this->tokens[$idx]->textContent = $text;
	}

	public function updateSpaceAfter(int $idx, bool $spaceAfter)
	{
		$this->tokens[$idx]->spaceAfter = $spaceAfter;
	}

	public function removeToken(int $idx)
	{
		$tokens = [];
		for ($i = 0; $i < count($this->tokens); $i++)
		{
			if ($i != $idx)
				$tokens[] = $this->tokens[$i];
		}
		$this->tokens = $tokens;
	}

	public function newSamplePerson()
	{
		$this->samplePerson = Person::join('model_has_roles', 'model_has_roles.model_id', '=', 'people.id')
		                            ->where('model_has_roles.role_id', $this->role->id)
		                            ->inRandomOrder()
		                            ->first();
	}

	public function saveName()
	{
		$nConstructor = new NameConstructor($this->tokens);
		if ($this->role->name == SchoolRoles::$STUDENT)
			$this->settings->studentName = $nConstructor;
		elseif ($this->role->name == SchoolRoles::$EMPLOYEE)
			$this->settings->employeeName = $nConstructor;
		else
			$this->settings->parentName = $nConstructor;
		$this->settings->save();
		session()->flash('success-status', __('system.settings.names.updated', ['role' => $this->role->name]));
		return $this->redirect(route('settings.school'));
	}

	public function resetName()
	{
		if ($this->role->name == SchoolRoles::$STUDENT)
			$nConstructor = $this->settings->studentName;
		elseif ($this->role->name == SchoolRoles::$EMPLOYEE)
			$nConstructor = $this->settings->employeeName;
		else
			$nConstructor = $this->settings->parentName;
		$this->tokens = $nConstructor->tokens;
	}

	public function updateOrder($id, $position)
	{
		$token = array_splice($this->tokens, $id, 1, []);
		array_splice($this->tokens, $position, 0, $token);
	}
};
?>
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ $role->name . " " . __('people.name') }}</h4>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="text-muted fw-bold me-2">{{ __('Add Tokens') }}:</span>
                <button type="button" class="btn btn-primary btn-sm shadow-sm"
                        wire:click="addBasicToken()">
                    <i class="fa-solid fa-plus-circle me-1"></i>{{ __('people.name.creator.basic') }}
                </button>
                <button type="button" class="btn btn-info text-white btn-sm shadow-sm"
                        wire:click="addRoleToken()">
                    <i class="fa-solid fa-plus-circle me-1"></i>{{ __('people.name.creator.role') }}
                </button>
                <button type="button" class="btn btn-secondary btn-sm shadow-sm"
                        wire:click="addTextToken()">
                    <i class="fa-solid fa-plus-circle me-1"></i>{{ __('people.name.creator.text') }}
                </button>
            </div>

            <div class="d-flex flex-wrap align-items-center bg-light p-3 rounded border mb-4" wire:sort="updateOrder"
                 style="min-height: 80px;">
                @forelse($tokens as $token)
                    <div class="p-1 m-1 bg-white border rounded shadow-sm" wire:key="{{ $loop->index }}"
                         wire:sort:item="{{ $loop->index }}">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text show-as-grab cursor-pointer" wire:sort:handle
                                  title="Drag to reorder">
                                <i class="fa-solid fa-grip-vertical text-muted"></i>
                            </span>
                            @if($token->type == \App\Classes\People\NameToken::TYPE_BASIC_FIELD)
                                <select class="form-select"
                                        wire:change="updateBasicFieldName({{ $loop->index }}, $event.target.value)">
                                    @foreach(\App\Classes\People\NameToken::basicFields() as $field => $fieldName)
                                        <option
                                                value="{{ $field }}" @selected($field == $token->basicFieldName)>{{ $fieldName }}</option>
                                    @endforeach
                                </select>
                            @elseif($token->type == \App\Classes\People\NameToken::TYPE_ROLE_FIELD)
                                <select class="form-select"
                                        wire:change="updateRoleField({{ $loop->index }}, $event.target.value)">
                                    @foreach($role->fields as $field)
                                        <option value="{{ $field->fieldId }}">{{ $field->fieldName }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" value="{{ $token->textContent }}" class="form-control"
                                       placeholder="{{ __('people.name.creator.text') }}"
                                       wire:change="updateText({{ $loop->index }}, $event.target.value)"/>
                            @endif

                            <button type="button"
                                    class="btn @if($token->spaceAfter) btn-outline-success @else btn-outline-secondary @endif"
                                    wire:click="updateSpaceAfter({{ $loop->index }}, {{ $token->spaceAfter? 0: 1 }})"
                                    title="{{ __('people.name.creator.space') }}">
                                @if($token->spaceAfter)
                                    <i class="fa-solid fa-text-width"></i>
                                @else
                                    <i class="fa-solid fa-compress"></i>
                                @endif
                            </button>

                            <button type="button" class="btn btn-outline-danger"
                                    wire:click="removeToken({{ $loop->index }})" title="Remove">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-muted w-100 text-center py-2">
                        <i class="fa-solid fa-circle-info me-2"></i>{{ __('No tokens added yet.') }}
                    </div>
                @endforelse
            </div>

            <div class="card border-info mb-4 shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa-solid fa-eye me-2"></i>{{ __('people.name.creator.sample') }}</h5>
                    <button type="button" class="btn btn-light btn-sm text-info fw-bold"
                            wire:click="newSamplePerson()">
                        <i class="fa-solid fa-shuffle me-1"></i>{{ __('people.name.creator.random') }}
                    </button>
                </div>
                <div class="card-body text-center">
                    <h3 class="display-6 text-dark mb-0">{{ $this->sampleName }}</h3>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button type="button" class="btn btn-secondary px-4 shadow-sm"
                        wire:click="resetName()">
                    <i class="fa-solid fa-rotate-left me-2"></i>{{ __('common.revert.changes') }}
                </button>
                <button type="button" class="btn btn-success px-4 shadow-sm"
                        wire:click="saveName()">
                    <i class="fa-solid fa-check me-2"></i>{{ __('common.apply.changes') }}
                </button>
            </div>
        </div>
    </div>
</div>
