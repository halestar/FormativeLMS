<?php

namespace App\Traits;

use App\Models\CRUD\Level;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Leveable
{
	public function levels(): MorphToMany
	{
		return $this->morphToMany(Level::class, 'leveable', 'leveables')
		            ->orderBy('order');
	}
}
