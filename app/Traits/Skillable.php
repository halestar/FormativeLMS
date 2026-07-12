<?php

namespace App\Traits;

use App\Models\SubjectMatter\Assessment\Skill;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Skillable
{
	public function skills(): MorphToMany
	{
		return $this->morphToMany(Skill::class, 'skillable');
	}
}
