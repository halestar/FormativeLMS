<?php

namespace App\Traits;

use App\Models\People\ViewPolicies\ViewableField;

trait HasViewableFields
{
    public function viewableField(string $field): ViewableField
    {
        return ViewableField::where('parent_class', get_class($this))->where('field', $field)->first();
    }
}
