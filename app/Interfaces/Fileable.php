<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Fileable
{
	public function workFiles(): MorphToMany;
	
}
