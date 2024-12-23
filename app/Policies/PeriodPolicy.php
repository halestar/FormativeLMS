<?php

namespace App\Policies;

use App\Models\Locations\Campus;
use App\Models\People\Person;
use App\Models\Schedules\Period;

class PeriodPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Person $person, Campus $campus): bool
    {
        return ($person->can('locations.periods') &&
            $person->isEmployee() &&
            $person->employeeCampuses()->where('campus_id', $campus->id)->exists());
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Person $person, Period $period): bool
    {
        return ($person->can('locations.periods') &&
            $person->isEmployee() &&
            $person->employeeCampuses()->where('campus_id', $period->campus_id)->exists());
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Person $person, Campus $campus): bool
    {
        return ($person->can('locations.periods') &&
            $person->isEmployee() &&
            $person->employeeCampuses()->where('campus_id', $campus->id)->exists());
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Person $person, Period $period): bool
    {
        return ($person->can('locations.periods') &&
            $person->isEmployee() &&
            $person->employeeCampuses()->where('campus_id', $period->campus_id)->exists());
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Person $person, Period $period): bool
    {
        return ($person->can('subjects.subjects') &&
            $person->isEmployee() &&
            $person->employeeCampuses()->where('campus_id', $period->campus_id)->exists() &&
            $period->canDelete());
    }

}
