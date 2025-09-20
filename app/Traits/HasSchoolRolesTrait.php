<?php
namespace App\Traits;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Traits\HasRoles;

trait HasSchoolRolesTrait
{
	use HasRoles;
	public function schoolRoles(): BelongsToMany
	{
		return $this->roles()->withPivot('field_values');
	}
}