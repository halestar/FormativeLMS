<?php

namespace App\Traits;

use App\Models\People\PersonRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Traits\HasRoles;

trait HasSchoolRolesTrait
{
	use HasRoles;

	public function schoolRoles(): BelongsToMany
	{
		return $this->roles()
		            ->using(PersonRole::class)
		            ->withPivot('field_values');
	}
}