<?php

namespace App\Traits;

use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\PermissionRegistrar;
use function Illuminate\Support\enum_value;

trait HasDefaultRoles
{
	public function defaultRoles(): MorphToMany
	{
		return $this->morphToMany(
			config('permission.models.role'),
			'model',
			'default_roles',
			'model_id',
			app(PermissionRegistrar::class)->pivotRole
		);
	}

	#[Scope]
	protected function defaultRole(Builder $query, $roles, ?string $guard = null, bool $without = false): void
	{
		if ($roles instanceof Collection)
			$roles = $roles->all();

		$roles = array_map(function ($role) use ($guard) {
			if ($role instanceof Role) {
				return $role;
			}

			$role = enum_value($role);

			$method = is_int($role) || PermissionRegistrar::isUid($role) ? 'findById' : 'findByName';

			return SchoolRoles::{$method}($role, $guard ?: "web");
		}, Arr::wrap($roles));

		$key = (new SchoolRoles())->getKeyName();

		$query->{! $without ? 'whereHas' : 'whereDoesntHave'}('roles', fn (Builder $subQuery) => $subQuery
			->whereIn("default_roles.$key", array_column($roles, $key))
		);
	}

	#[Scope]
	protected function withoutDefaultRole(Builder $query, $roles, ?string $guard = null): void
	{
		$this->defaultRole($query, $roles, $guard, true);
	}

	public function resetRoles()
	{
		$this->roles()->detach();
		$this->roles()->sync($this->defaultRoles->pluck('id')->toArray());
	}

}
