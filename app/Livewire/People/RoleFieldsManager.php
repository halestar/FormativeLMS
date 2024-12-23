<?php

namespace App\Livewire\People;

use App\Classes\RoleField;
use App\Models\Utilities\SchoolRoles;
use App\Rules\UniqueJsonName;
use Illuminate\Validation\Rule;
use Livewire\Component;

class RoleFieldsManager extends Component
{
    public ?int $role_id = null;
    public ?SchoolRoles $role = null;
    public ?string $fieldName = null;
    public string $fieldType = RoleField::TEXT;
    public ?string $fieldHelp = null;
    public null|string|array $fieldPlaceholder = null;
    public array $fieldOptions = [];
    public ?RoleField $fieldPreview = null;

    protected function rules()
    {
        $rules = [
            'fieldName' => ['required', 'min:3', 'max:255', new UniqueJsonName($this->role)],
            'fieldType' => ['required', Rule::in(array_keys(RoleField::FIELDS))],
        ];
        if($this->fieldType == RoleField::CHECKBOX || $this->fieldType == RoleField::RADIO || $this->fieldType == RoleField::SELECT)
        {
            $rules['fieldOptions'] =  ['required', 'array', 'min:2'];
        }

        return $rules;
    }

    public function loadRole()
    {
        if($this->role_id)
            $this->role = SchoolRoles::find($this->role_id);
        else
            $this->role = null;
        $this->fieldName = null;
        $this->fieldType = RoleField::TEXT;
        $this->fieldHelp = null;
        $this->fieldPlaceholder = null;
        $this->fieldOptions = [];
        $this->updatePreview();
    }

    public function updatePreview()
    {
        if($this->fieldType == RoleField::CHECKBOX && !is_array($this->fieldPlaceholder))
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

    public function addField()
    {
        $this->validate();
        $existingFields = $this->role->fields;
        $existingFields[] = $this->fieldPreview;
        $this->role->fields = $existingFields;
        $this->role->save();
        // and we sync the field permissions
        $this->role->syncFieldPermissions();
        $this->loadRole();
    }

    public function addOption(string $option)
    {
        $this->fieldOptions[] = $option;
        $this->updatePreview();
    }

    public function removeOption(string $option)
    {
        $this->fieldOptions = array_diff($this->fieldOptions, [$option]);
        if(!$this->fieldOptions)
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
        if($role_id)
            $role = SchoolRoles::find($role_id);
        if($role)
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
        if($role_id)
            $role = SchoolRoles::find($role_id);
        if($role)
        {
            $fieldToCopy = $this->role->fields[$fieldId];
            if($fieldToCopy)
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
    public function render()
    {
        return view('livewire.people.role-fields-manager');
    }
}
