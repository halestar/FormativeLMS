<?php

namespace App\Policies;

use App\Models\People\Person;

class PersonPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Person $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Person $user, Person $person): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Person $user): bool
    {

        return $user->can('people.create');
    }
    /**
     * Determine whether the user can edit a profile
     */
    public function edit(Person $user, Person $person): bool
    {
        if($user->id == $person->id)
            return true;
		if($person->hasRole('Super Admin'))
			return false;
        return $user->can('people.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Person $user, Person $person): bool
    {
        return $user->can('people.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Person $user, Person $person): bool
    {
        return $user->can('people.delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Person $user, Person $person): bool
    {
        return $user->can('people.delete');
    }

	public function changeSelfPassword(Person $user, Person $person): bool
	{
		if($user->can('people.password'))
			return true;
		if($user->id == $person->id)
			return true;
		return false;
	}
}
