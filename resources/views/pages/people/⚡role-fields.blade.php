<?php

use App\Classes\People\RoleField;
use App\Classes\SessionSettings;
use App\Models\Utilities\SchoolRoles;
use App\Rules\UniqueJsonName;
use App\Traits\FullPageComponent;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
	use FullPageComponent;

	public ?int $role_id = null;
	public ?SchoolRoles $role = null;
	public ?string $fieldName = null;
	public string $fieldType = RoleField::TEXT;
	public ?string $fieldHelp = null;
	public null|string|array $fieldPlaceholder = null;
	public array $fieldOptions = [];
	public ?RoleField $fieldPreview = null;
	public bool $editing = false;

	public function mount()
	{
		$this->authorize('has-permission', 'people.roles.fields');
		$this->breadcrumb = [__('people.fields.roles') => '#'];
		$this->role_id = SessionSettings::get('people.role_fields.role_id', null);
		$this->loadRole();
	}

	protected function rules()
	{
		$rules = [
			'fieldType' => ['required', Rule::in(array_keys(RoleField::FIELDS))],
		];
		if ($this->editing)
			$rules['fieldName'] = ['required', 'min:3', 'max:255'];
		else
			$rules['fieldName'] = ['required', 'min:3', 'max:255', new UniqueJsonName($this->role)];
		if ($this->fieldType == RoleField::CHECKBOX || $this->fieldType == RoleField::RADIO ||
		    $this->fieldType == RoleField::SELECT)
		{
			$rules['fieldOptions'] = ['required', 'array', 'min:2'];
		}

		return $rules;
	}

	public function addField()
	{
		$this->validate();
		$existingFields = $this->role->fields;
		$existingFields[$this->fieldPreview->fieldId] = $this->fieldPreview;
		$this->role->fields = $existingFields;
		$this->role->save();
		// and we sync the field permissions
		$this->role->syncFieldPermissions();
		$this->loadRole();
	}

	public function loadField(string $fieldId)
	{
		$field = $this->role->fields[$fieldId];
		if ($field && $field instanceof RoleField)
		{
			$this->fieldPreview = $field;
			$this->fieldName = $field->fieldName;
			$this->fieldType = $field->fieldType;
			$this->fieldHelp = $field->fieldHelp;
			$this->fieldPlaceholder = $field->fieldPlaceholder;
			$this->fieldOptions = $field->fieldOptions;
			$this->editing = true;
		}
	}

	public function loadRole()
	{
		if ($this->role_id)
			$this->role = SchoolRoles::find($this->role_id);
		else
			$this->role = null;
		$this->fieldName = null;
		$this->fieldType = RoleField::TEXT;
		$this->fieldHelp = null;
		$this->fieldPlaceholder = null;
		$this->fieldOptions = [];
		$this->editing = false;
		$this->updatePreview();
		SessionSettings::set('people.role_fields.role_id', $this->role_id);
	}

	public function updatePreview()
	{
		if ($this->fieldType == RoleField::CHECKBOX && !is_array($this->fieldPlaceholder))
			$this->fieldPlaceholder = [];
		$attr =
			[
				'fieldName' => $this->fieldName,
				'fieldType' => $this->fieldType,
				'fieldHelp' => $this->fieldHelp,
				'fieldPlaceholder' => $this->fieldPlaceholder,
				'fieldOptions' => $this->fieldOptions,
			];
		$this->fieldPreview = new RoleField($attr);
	}

	public function addOption(string $option)
	{
		$this->fieldOptions[] = $option;
		$this->updatePreview();
	}

	public function removeOption(string $option)
	{
		$this->fieldOptions = array_diff($this->fieldOptions, [$option]);
		if (!$this->fieldOptions)
			$this->fieldOptions = [];
		$this->updatePreview();
	}

	public function removeField(string $fieldId)
	{
		$fields = $this->role->fields;
		unset($fields[$fieldId]);
		$this->role->fields = $fields;
		$this->role->save();
		// and we sync the field permissions
		$this->role->syncFieldPermissions();
	}

	public function copyAllToRole($role_id)
	{
		if ($role_id)
			$role = SchoolRoles::find($role_id);
		if ($role)
		{
			$existingFields = $role->fields;
			$existingFields = array_merge($this->role->fields, $existingFields);
			$role->fields = $existingFields;
			$role->save();
			// and we sync the field permissions
			$role->syncFieldPermissions();
			$this->role_id = $role_id;
			$this->loadRole();
		}
	}

	public function copyFieldToRole($fieldId, $role_id)
	{
		if ($role_id)
			$role = SchoolRoles::find($role_id);
		if ($role)
		{
			$fieldToCopy = $this->role->fields[$fieldId];
			if ($fieldToCopy)
			{
				$existingFields = $role->fields;
				$existingFields[$fieldId] = $fieldToCopy;
				$role->fields = $existingFields;
				$role->save();
				// and we sync the field permissions
				$role->syncFieldPermissions();
				$this->role_id = $role_id;
				$this->loadRole();
			}
		}
	}
};
?>
<div class="container">
    <div class="row justify-content-center mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('people.fields.permissions') }}" wire:navigate class="btn btn-outline-info">
                            <i class="fa-solid fa-lock me-1"></i> Permissions
                        </a>
                    </div>
                    <div class="input-group input-group-lg">
                        <label for="role_id" class="input-group-text bg-primary text-white border-primary"><i
                                    class="fa-solid fa-users me-2"></i> {{ __('people.roles.field.select') }}</label>
                        <select name="role_id" id="role_id" class="form-select border-primary" wire:model="role_id"
                                wire:change="loadRole()">
                            <option>{{ __('people.roles.select') }}</option>
                            @foreach (\App\Models\Utilities\SchoolRoles::all() as $schoolRole)
                                <option value="{{ $schoolRole->id }}">{{ $schoolRole->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($role_id)
        <div class="row g-4">
            <!-- Create Column -->
            <div class="col-lg-4 col-md-12">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h4 class="text-primary fw-bold mb-0">
                            @if($editing)
                                <i class="fa-solid fa-plus-circle me-2"></i>{{ __('people.roles.field.update') }}
                            @else
                                <i class="fa-solid fa-plus-circle me-2"></i>{{ __('people.roles.field.create') }}
                            @endif
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="fieldName" class="form-label">{{ __('people.roles.field.name') }}</label>
                            <input
                                    type="text"
                                    name="fieldName"
                                    id="fieldName"
                                    wire:model="fieldName"
                                    class="form-control @error('fieldName') is-invalid @enderror"
                                    wire:change="updatePreview()"
                            />
                            <x-utilities.error-display
                                    key="fieldName">{{ $errors->first('fieldName') }}</x-utilities.error-display>
                        </div>
                        <div class="mb-3">
                            <label for="fieldType" class="form-label">{{ __('people.roles.field.name') }}</label>
                            <select
                                    type="text"
                                    name="fieldType"
                                    id="fieldType"
                                    wire:model="fieldType"
                                    class="form-select @error('fieldType') is-invalid @enderror"
                                    wire:change="updatePreview()"
                            >
                                @foreach(\App\Classes\People\RoleField::FIELDS as $id => $name)
                                    <option value="{{ $id }}">{{ __($name) }}</option>
                                @endforeach
                            </select>
                            <x-utilities.error-display
                                    key="fieldType">{{ $errors->first('fieldType') }}</x-utilities.error-display>
                        </div>
                        <div class="mb-3">
                            <label for="fieldHelp" class="form-label">{{ __('people.roles.field.help') }}</label>
                            <textarea type="text" name="fieldHelp" id="fieldHelp" wire:model="fieldHelp"
                                      class="form-control"
                                      wire:change="updatePreview()"></textarea>
                        </div>
                        @if($fieldType == \App\Classes\People\RoleField::CHECKBOX || $fieldType == \App\Classes\People\RoleField::SELECT ||
                            $fieldType == \App\Classes\People\RoleField::RADIO)

                            @if($fieldType == \App\Classes\People\RoleField::CHECKBOX)
                                <div class="mb-3">
                                    <label class="form-label">{{ __('common.default') }}</label>
                                    <br/>
                                    @foreach($fieldOptions as $option)
                                        <div class="form-check form-check-inline">
                                            <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="default_{{ $loop->iteration }}"
                                                    wire:model="fieldPlaceholder"
                                                    value="{{ $option }}"
                                                    wire:click="updatePreview()"
                                            />
                                            <label class="form-check-label"
                                                   for="default_{{ $loop->iteration }}">{{ $option }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="mb-3">
                                    <label for="fieldPlaceholder" class="form-label">{{ __('common.default') }}</label>
                                    <select id="fieldPlaceholder" wire:model="fieldPlaceholder" class="form-select"
                                            wire:change="updatePreview()">
                                        <option>{{ __('common.default.no') }}</option>
                                        @foreach($fieldOptions as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="fieldOptions"
                                       class="form-label">{{ __('people.roles.field.options') }}</label>
                                <div class="input-group">
                                    <label for="newOption" class="input-group-text">{{ __('common.option') }}:</label>
                                    <input type="text" id="newOption" class="form-control"
                                           wire:keyup.enter="addOption($('#newOption').val());$('#newOption').val('')"/>
                                    <button type="button" class="btn btn-primary"
                                            wire:click="addOption($('#newOption').val());$('#newOption').val('')">{{ __('common.add') }}
                                    </button>
                                </div>
                                <select
                                        id="fieldOptions"
                                        name="fieldOptions"
                                        size="5"
                                        class="form-select @error('fieldOptions') is-invalid @enderror"
                                        aria-describedby="optionsHelp"
                                        wire:click="removeOption($event.target.value)"
                                >
                                    @foreach($fieldOptions as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                                <div id="optionsHelp" class="form-text">
                                    {{ __('people.roles.field.options.help') }}
                                </div>
                                <x-utilities.error-display
                                        key="fieldOptions">{{ $errors->first('fieldOptions') }}</x-utilities.error-display>
                            </div>
                        @else
                            <div class="mb-3">
                                <label for="fieldPlaceholder"
                                       class="form-label">{{ __('people.roles.field.placeholder') }}</label>
                                <input type="text" name="fieldPlaceholder" id="fieldPlaceholder"
                                       wire:model="fieldPlaceholder"
                                       class="form-control" wire:change="updatePreview()"/>
                            </div>
                        @endif
                        <div class="d-grid gap-2 mt-4">
                            @if($editing)
                                <button type="button" class="btn btn-primary"
                                        wire:click="addField()">
                                    <i class="fa-solid fa-save me-1"></i> {{ __('people.roles.field.update') }}
                                </button>
                                <button type="button" class="btn btn-secondary"
                                        wire:click="loadRole()">
                                    <i class="fa-solid fa-save me-1"></i> {{ __('common.clear') }}
                                </button>
                            @else
                                <button type="button" class="btn btn-primary"
                                        wire:click="addField()">
                                    <i class="fa-solid fa-save me-1"></i> {{ __('people.roles.field.add') }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- Preview Column -->
            <div class="col-lg-4 col-md-12">
                <div class="card shadow-sm h-100 border-0 bg-light">
                    <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                        <h4 class="text-secondary fw-bold mb-0"><i
                                    class="fa-solid fa-eye me-2"></i>{{ __('people.roles.field.preview') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="p-3 bg-white rounded border">
                            {!! $fieldPreview?->getHtml() ?? "" !!}
                        </div>
                    </div>
                </div>
            </div>
            <!-- Existing Column -->
            <div class="col-lg-4 col-md-12">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h4 class="text-success fw-bold mb-0"><i
                                    class="fa-solid fa-list-check me-2"></i>{{ __('people.roles.field.existing') }}</h4>
                    </div>
                    <div class="card-body">
                        @if($role)
                            <div class="input-group mb-3">
                                <label for="copy_id"
                                       class="input-group-text">{{ trans_choice('people.roles.field.copy', 2) }}</label>
                                <select name="copy_id" id="copy_id" class="form-select">
                                    <option>{{ __('people.roles.select') }}</option>
                                    @foreach (\App\Models\Utilities\SchoolRoles::where('id', '<>', $role_id)->get() as $schoolRole)
                                        <option value="{{ $schoolRole->id }}">{{ $schoolRole->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-primary"
                                        wire:click="copyAllToRole($('#copy_id').val())">
                                    {{ __('common.copy') }}</button>
                            </div>
                            <ul class="list-group">
                                @foreach($role->fields as $field)
                                    <li class="list-group-item p-3" wire:key="{{ $field->fieldName }}">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="field-container flex-grow-1 me-3">
                                                {!! $field->getHTML() !!}
                                            </div>
                                            <div>
                                                <button
                                                        type="button"
                                                        class="btn btn-outline-primary btn-sm rounded-circle"
                                                        wire:click="loadField('{{ $field->fieldId }}')"
                                                ><i class="fa-solid fa-edit"></i></button>
                                                <button
                                                        type="button"
                                                        class="btn btn-outline-danger btn-sm rounded-circle"
                                                        wire:confirm="Are you sure you wish to delete this form field?"
                                                        wire:click="removeField('{{ $field->fieldId }}')"
                                                ><i class="fa-solid fa-trash"></i></button>
                                            </div>
                                        </div>
                                        <div class="input-group input-group-sm">
                                            <label for="copy_id_{{ $loop->iteration }}"
                                                   class="input-group-text bg-light">{{ trans_choice('people.roles.field.copy', 1) }}</label>
                                            <select id="copy_id_{{ $loop->iteration }}" class="form-select">
                                                <option>Select a Role</option>
                                                @foreach (\App\Models\Utilities\SchoolRoles::where('id', '<>', $role_id)->get() as $schoolRole)
                                                    <option value="{{ $schoolRole->id }}">{{ $schoolRole->name }}</option>
                                                @endforeach
                                            </select>
                                            <button
                                                    type="button"
                                                    class="btn btn-secondary"
                                                    wire:click="copyFieldToRole('{{ $field->fieldId }}', $('#copy_id_{{ $loop->iteration }}').val())"
                                            ><i class="fa-solid fa-copy me-1"></i>{{ __('common.copy') }}</button>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
