<?php

namespace App\Policies;

use App\Models\People\Person;
use App\Models\People\ViewPolicies\ViewableField;

class ViewingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function canView(Person $viewer, ViewableField $field): bool
    {
        return true;
    }
}
