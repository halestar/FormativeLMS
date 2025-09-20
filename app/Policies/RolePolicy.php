<?php

namespace App\Policies;

use App\Models\People\Person;
use App\Models\Utilities\SchoolRole;
use App\Models\Utilities\SchoolRoles;

class RolePolicy
{
	/**
	 * Determine whether the user can view any models.
	 */
	public function viewAny(Person $person): bool
	{
		return $person->can('settings.roles.view');
	}
	
	/**
	 * Determine whether the user can view the model.
	 */
	public function view(Person $person, SchoolRoles $role): bool
	{
		return $person->can('settings.roles.view');
	}
	
	/**
	 * Determine whether the user can create models.
	 */
	public function create(Person $person): bool
	{
		return $person->can('settings.roles.create');
	}
	
	/**
	 * Determine whether the user can update the model.
	 */
	public function update(Person $person, SchoolRoles $role): bool
	{
		return $person->can('settings.roles.update');
	}
	
	/**
	 * Determine whether the user can delete the model.
	 */
	public function delete(Person $person, SchoolRoles $role): bool
	{
		return $person->can('settings.roles.delete') && !$role->base_role;
	}
	
}
