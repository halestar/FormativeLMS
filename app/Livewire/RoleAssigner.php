<?php

namespace App\Livewire;

use App\Interfaces\HasSchoolRoles;
use App\Models\Utilities\SchoolRoles;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class RoleAssigner extends Component
{
    public HasSchoolRoles $attachObj;
    public array $baseRoles = [];
    public bool $editing = false;
	public bool $editorOnly = false;
	public bool $disabled = false;

    private function setBaseRoles(): void
    {
        $roles = SchoolRoles::baseRoles()->get();
        $this->baseRoles =
            [
                [
                    'id' => $roles->where('name', SchoolRoles::$EMPLOYEE)->first()->id,
                    'name' => SchoolRoles::$EMPLOYEE,
                    'hasRole' => $this->attachObj->hasRole(SchoolRoles::$EMPLOYEE),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$FACULTY)->first()->id,
                    'name' => SchoolRoles::$FACULTY,
                    'hasRole' => $this->attachObj->hasRole(SchoolRoles::$FACULTY),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$STAFF)->first()->id,
                    'name' => SchoolRoles::$STAFF,
                    'hasRole' => $this->attachObj->hasRole(SchoolRoles::$STAFF),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$COACH)->first()->id,
                    'name' => SchoolRoles::$COACH,
                    'hasRole' => $this->attachObj->hasRole(SchoolRoles::$COACH),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$STUDENT)->first()->id,
                    'name' => SchoolRoles::$STUDENT,
                    'hasRole' => $this->attachObj->hasRole(SchoolRoles::$STUDENT),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$PARENT)->first()->id,
                    'name' => SchoolRoles::$PARENT,
                    'hasRole' => $this->attachObj->hasRole(SchoolRoles::$PARENT),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$OLD_FACULTY)->first()->id,
                    'name' => SchoolRoles::$OLD_FACULTY,
                    'hasRole' => $this->attachObj->hasRole(SchoolRoles::$OLD_FACULTY),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$OLD_STAFF)->first()->id,
                    'name' => SchoolRoles::$OLD_STAFF,
                    'hasRole' => $this->attachObj->hasRole(SchoolRoles::$OLD_STAFF),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$OLD_COACH)->first()->id,
                    'name' => SchoolRoles::$OLD_COACH,
                    'hasRole' => $this->attachObj->hasRole(SchoolRoles::$OLD_COACH),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$OLD_STUDENT)->first()->id,
                    'name' => SchoolRoles::$OLD_STUDENT,
                    'hasRole' => $this->attachObj->hasRole(SchoolRoles::$OLD_STUDENT),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$OLD_PARENT)->first()->id,
                    'name' => SchoolRoles::$OLD_PARENT,
                    'hasRole' => $this->attachObj->hasRole(SchoolRoles::$OLD_PARENT),
                ],
            ];
    }

    public function mount(HasSchoolRoles $attachObj, bool $editorOnly = false, bool $disabled = false)
    {
        $this->attachObj = $attachObj;
        $this->setBaseRoles();
		$this->disabled = $disabled;
		$this->editorOnly = $editorOnly;
		if($this->editorOnly)
			$this->editing = true;
    }

    public function changeBaseRole(SchoolRoles $role, bool $active): void
    {
        // first, we toggle the actual role
        if($active)
            $this->attachObj->assignRole($role);
        else
            $this->attachObj->removeRole($role);

        // Base roles have consequences. here we switch them based on what
        // they changed
        if($role->name == SchoolRoles::$EMPLOYEE)
        {
            // this is a big one. If we're deactivating it, we must remove any Staff/Coach/Faculty
            // role and set it to old version. if we're activating it, we don't need to do anything
            if (!$active)
            {
                if ($this->attachObj->hasRole(SchoolRoles::$COACH))
                {
                    $this->attachObj->removeRole(SchoolRoles::$COACH);
                    $this->attachObj->assignRole(SchoolRoles::$OLD_COACH);
                }
                if ($this->attachObj->hasRole(SchoolRoles::$FACULTY))
                {
                    $this->attachObj->removeRole(SchoolRoles::$FACULTY);
                    $this->attachObj->assignRole(SchoolRoles::$OLD_FACULTY);
                }
                if ($this->attachObj->hasRole(SchoolRoles::$STAFF))
                {
                    $this->attachObj->removeRole(SchoolRoles::$STAFF);
                    $this->attachObj->assignRole(SchoolRoles::$OLD_STAFF);
                }
            }
        }
        elseif($role->name == SchoolRoles::$STUDENT)
        {
            // in this case we add the old student role if we're deactivating them
            // or we remove it if we're reactivating them
            if($active)
                $this->attachObj->removeRole(SchoolRoles::$OLD_STUDENT);
            else
                $this->attachObj->assignRole(SchoolRoles::$OLD_STUDENT);
        }
        elseif($role->name == SchoolRoles::$FACULTY)
        {
            // in this case we add the old faculty role if we're deactivating them
            // or we remove it if we're reactivating them
            if($active)
                $this->attachObj->removeRole(SchoolRoles::$OLD_FACULTY);
            else
                $this->attachObj->assignRole(SchoolRoles::$OLD_FACULTY);
            //if we activating it, but we don't have the $employee role, we need to add it.
            if($active && !$this->attachObj->hasRole(SchoolRoles::$EMPLOYEE))
                $this->attachObj->assignRole(SchoolRoles::$EMPLOYEE);
        }
        elseif($role->name == SchoolRoles::$STAFF)
        {
            // in this case we add the old faculty role if we're deactivating them
            // or we remove it if we're reactivating them
            if($active)
                $this->attachObj->removeRole(SchoolRoles::$OLD_STAFF);
            else
                $this->attachObj->assignRole(SchoolRoles::$OLD_STAFF);
            //if we activating it, but we don't have the $employee role, we need to add it.
            if($active && !$this->attachObj->hasRole(SchoolRoles::$EMPLOYEE))
                $this->attachObj->assignRole(SchoolRoles::$EMPLOYEE);
        }
        elseif($role->name == SchoolRoles::$COACH)
        {
            // in this case we add the old coach role if we're deactivating them
            // or we remove it if we're reactivating them
            if($active)
                $this->attachObj->removeRole(SchoolRoles::$OLD_COACH);
            else
                $this->attachObj->assignRole(SchoolRoles::$OLD_COACH);
            //if we activating it, but we don't have the $employee role, we need to add it.
            if($active && !$this->attachObj->hasRole(SchoolRoles::$EMPLOYEE))
                $this->attachObj->assignRole(SchoolRoles::$EMPLOYEE);
        }
        elseif($role->name == SchoolRoles::$PARENT)
        {
            // in this case we add the old parent role if we're deactivating them
            // or we remove it if we're reactivating them
            if($active)
                $this->attachObj->removeRole(SchoolRoles::$OLD_PARENT);
            else
                $this->attachObj->assignRole(SchoolRoles::$OLD_PARENT);
        }
        $this->setBaseRoles();
    }

    public function changeNormalRole(SchoolRoles $role, bool $active): void
    {
        if($active)
            $this->attachObj->assignRole($role);
        else
            $this->attachObj->removeRole($role);
    }


    public function render()
    {
        return view('livewire.role-assigner');
    }
	
	#[Computed(persist: true)]
	public function ifDisabled(): string
	{
		return $this->disabled? 'disabled' : '';
	}
	
	#[On('role-assigner.refresh-roles')]
	public function refreshRoles()
	{
		$this->attachObj->fresh();
		$this->attachObj->load('roles');
		$this->setBaseRoles();
	}
	
	#[On('role-assigner.disable')]
	public function disable()
	{
		$this->disabled = true;
		unset($this->ifDisabled);
	}
	
	#[On('role-assigner.enable')]
	public function enable()
	{
		$this->disabled = false;
		unset($this->ifDisabled);
	}
}
