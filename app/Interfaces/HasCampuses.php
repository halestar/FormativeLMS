<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface HasCampuses
{
    public function campuses(): MorphToMany;
}
