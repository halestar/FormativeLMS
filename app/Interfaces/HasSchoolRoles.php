<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface HasSchoolRoles
{
	public function schoolRoles(): BelongsToMany;
	
	public function hasRole($roles, ?string $guard = null): bool;
	
	public function assignRole(...$roles);
	
	public function removeRole(...$role);
	
	public function __toString();
	
}
