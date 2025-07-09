<?php

namespace App\Traits;

use App\Models\Locations\Campus;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Campuseable
{
    public function campuses(): MorphToMany
    {
        return $this->morphToMany(Campus::class, 'campusable', 'campusables');
    }
}
