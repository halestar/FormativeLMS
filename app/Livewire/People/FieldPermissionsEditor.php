<?php

namespace App\Livewire\People;

use App\Models\People\FieldPermission;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Support\Collection;
use Livewire\Component;

class FieldPermissionsEditor extends Component
{
	public int $role_id;
	public ?SchoolRoles $selectedRole;
	public Collection $fields;
	
	public function mount()
	{
		$this->role_id = 0;
		$this->loadRole();
	}
	
	public function loadRole()
	{
		if($this->role_id == 0)
		{
			$this->selectedRole = null;
			$this->fields = FieldPermission::whereNull('role_id')
			                               ->get();
		}
		else
		{
			$this->selectedRole = SchoolRoles::find($this->role_id);
			$this->fields = FieldPermission::where('role_id', $this->role_id)
			                               ->get();
		}
	}
	
	public function toggleField(FieldPermission $field, string $permission)
	{
		$field->$permission = !$field->$permission;
		$field->save();
	}
	
	public function render()
	{
		return view('livewire.people.field-permissions-editor');
	}
}
