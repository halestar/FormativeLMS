<?php

namespace App\Policies;

use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;

class ClassSessionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Person $person): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Person $person, ClassSession $classSession): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Person $person): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Person $person, ClassSession $classSession): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Person $person, ClassSession $classSession): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Person $person, ClassSession $classSession): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Person $person, ClassSession $classSession): bool
    {
        return false;
    }
}
