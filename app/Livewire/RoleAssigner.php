<?php

namespace App\Livewire;

use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Support\Collection;
use Livewire\Component;

class RoleAssigner extends Component
{
    public Person $person;
    public array $baseRoles = [];
    public bool $editing = false;

    private function setBaseRoles(): void
    {
        $roles = SchoolRoles::baseRoles()->get();
        $this->baseRoles =
            [
                [
                    'id' => $roles->where('name', SchoolRoles::$EMPLOYEE)->first()->id,
                    'name' => SchoolRoles::$EMPLOYEE,
                    'hasRole' => $this->person->hasRole(SchoolRoles::$EMPLOYEE),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$FACULTY)->first()->id,
                    'name' => SchoolRoles::$FACULTY,
                    'hasRole' => $this->person->hasRole(SchoolRoles::$FACULTY),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$STAFF)->first()->id,
                    'name' => SchoolRoles::$STAFF,
                    'hasRole' => $this->person->hasRole(SchoolRoles::$STAFF),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$COACH)->first()->id,
                    'name' => SchoolRoles::$COACH,
                    'hasRole' => $this->person->hasRole(SchoolRoles::$COACH),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$STUDENT)->first()->id,
                    'name' => SchoolRoles::$STUDENT,
                    'hasRole' => $this->person->hasRole(SchoolRoles::$STUDENT),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$PARENT)->first()->id,
                    'name' => SchoolRoles::$PARENT,
                    'hasRole' => $this->person->hasRole(SchoolRoles::$PARENT),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$OLD_FACULTY)->first()->id,
                    'name' => SchoolRoles::$OLD_FACULTY,
                    'hasRole' => $this->person->hasRole(SchoolRoles::$OLD_FACULTY),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$OLD_STAFF)->first()->id,
                    'name' => SchoolRoles::$OLD_STAFF,
                    'hasRole' => $this->person->hasRole(SchoolRoles::$OLD_STAFF),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$OLD_COACH)->first()->id,
                    'name' => SchoolRoles::$OLD_COACH,
                    'hasRole' => $this->person->hasRole(SchoolRoles::$OLD_COACH),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$OLD_STUDENT)->first()->id,
                    'name' => SchoolRoles::$OLD_STUDENT,
                    'hasRole' => $this->person->hasRole(SchoolRoles::$OLD_STUDENT),
                ],
                [
                    'id' => $roles->where('name', SchoolRoles::$OLD_PARENT)->first()->id,
                    'name' => SchoolRoles::$OLD_PARENT,
                    'hasRole' => $this->person->hasRole(SchoolRoles::$OLD_PARENT),
                ],
            ];
    }

    public function mount(Person $person)
    {
        $this->person = $person;
        $this->setBaseRoles();
    }

    public function changeBaseRole(SchoolRoles $role, bool $active): void
    {
        // first, we toggle the actual role
        if($active)
            $this->person->assignRole($role);
        else
            $this->person->removeRole($role);

        // Base roles have consequences. here we switch them based on what
        // they changed
        if($role->name == SchoolRoles::$EMPLOYEE)
        {
            // this is a big one. If we're deactivating it, we must remove any Staff/Coach/Faculty
            // role and set it to old version. if we're activating it, we don't need to do anything
            if (!$active)
            {
                if ($this->person->hasRole(SchoolRoles::$COACH))
                {
                    $this->person->removeRole(SchoolRoles::$COACH);
                    $this->person->assignRole(SchoolRoles::$OLD_COACH);
                }
                if ($this->person->hasRole(SchoolRoles::$FACULTY))
                {
                    $this->person->removeRole(SchoolRoles::$FACULTY);
                    $this->person->assignRole(SchoolRoles::$OLD_FACULTY);
                }
                if ($this->person->hasRole(SchoolRoles::$STAFF))
                {
                    $this->person->removeRole(SchoolRoles::$STAFF);
                    $this->person->assignRole(SchoolRoles::$OLD_STAFF);
                }
            }
        }
        elseif($role->name == SchoolRoles::$STUDENT)
        {
            // in this case we add the old student role if we're deactivating them
            // or we remove it if we're reactivating them
            if($active)
                $this->person->removeRole(SchoolRoles::$OLD_STUDENT);
            else
                $this->person->assignRole(SchoolRoles::$OLD_STUDENT);
        }
        elseif($role->name == SchoolRoles::$FACULTY)
        {
            // in this case we add the old faculty role if we're deactivating them
            // or we remove it if we're reactivating them
            if($active)
                $this->person->removeRole(SchoolRoles::$OLD_FACULTY);
            else
                $this->person->assignRole(SchoolRoles::$OLD_FACULTY);
            //if we activating it, but we don't have the $employee role, we need to add it.
            if($active && !$this->person->hasRole(SchoolRoles::$EMPLOYEE))
                $this->person->assignRole(SchoolRoles::$EMPLOYEE);
        }
        elseif($role->name == SchoolRoles::$STAFF)
        {
            // in this case we add the old faculty role if we're deactivating them
            // or we remove it if we're reactivating them
            if($active)
                $this->person->removeRole(SchoolRoles::$OLD_STAFF);
            else
                $this->person->assignRole(SchoolRoles::$OLD_STAFF);
            //if we activating it, but we don't have the $employee role, we need to add it.
            if($active && !$this->person->hasRole(SchoolRoles::$EMPLOYEE))
                $this->person->assignRole(SchoolRoles::$EMPLOYEE);
        }
        elseif($role->name == SchoolRoles::$COACH)
        {
            // in this case we add the old coach role if we're deactivating them
            // or we remove it if we're reactivating them
            if($active)
                $this->person->removeRole(SchoolRoles::$OLD_COACH);
            else
                $this->person->assignRole(SchoolRoles::$OLD_COACH);
            //if we activating it, but we don't have the $employee role, we need to add it.
            if($active && !$this->person->hasRole(SchoolRoles::$EMPLOYEE))
                $this->person->assignRole(SchoolRoles::$EMPLOYEE);
        }
        elseif($role->name == SchoolRoles::$PARENT)
        {
            // in this case we add the old parent role if we're deactivating them
            // or we remove it if we're reactivating them
            if($active)
                $this->person->removeRole(SchoolRoles::$OLD_PARENT);
            else
                $this->person->assignRole(SchoolRoles::$OLD_PARENT);
        }
        $this->setBaseRoles();
    }

    public function changeNormalRole(SchoolRoles $role, bool $active): void
    {
        if($active)
            $this->person->assignRole($role);
        else
            $this->person->removeRole($role);
    }


    public function render()
    {
        return view('livewire.role-assigner');
    }
}
