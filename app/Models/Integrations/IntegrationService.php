<?php

namespace App\Models\Integrations;

use App\Casts\Utilities\AsJsonData;
use App\Enums\IntegratorServiceTypes;
use App\Interfaces\HasSchoolRoles;
use App\Models\People\Person;
use App\Traits\HasSchoolRolesTrait;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class IntegrationService extends Model implements HasSchoolRoles
{
	/*************************************************
	 * PROPERTIES
	 */
	use HasSchoolRolesTrait
	{
		HasSchoolRolesTrait::hasRole as traitHasRole;
		HasSchoolRolesTrait::schoolRoles as traitSchoolRoles;
	}
	public $timestamps = false;
	protected $table = "integration_services";
	protected $primaryKey = "id";
	public $incrementing = true;
	protected $fillable =
		[
			'name',
			'className',
			'path',
			'description',
			'service_type',
			'data',
			'enabled',
			'can_connect_to_people',
			'can_connect_to_system',
			'configurable',
			'inherit_permissions',
		];
	protected $guard_name = 'web';
	
	protected function casts(): array
	{
		return
			[
				'service_type' => IntegratorServiceTypes::class,
				'data' => AsJsonData::class,
				'enabled' => 'boolean',
				'can_connect_to_people' => 'boolean',
				'can_connect_to_system' => 'boolean',
				'configurable' => 'boolean',
				'inherit_permissions' => 'boolean',
			];
	}
	
	private ?IntegrationConnection $activeConnection = null;
	
	public function __toString()
	{
		return $this->name;
	}
	
	/*****************************************************************
	 * OVERRIDES
	 */
	
	public function newFromBuilder($attributes = [], $connection = null)
	{
		if($attributes instanceof \stdClass)
			$attributes = json_decode(json_encode($attributes), true);
		if($attributes['className'] == static::class)
			return parent::newFromBuilder($attributes, $connection);
		return (new $attributes['className'])->newFromBuilder($attributes, $connection);
	}
	
	public function hasRole($roles, ?string $guard = null): bool
	{
		if($this->inherit_permissions)
			return $this->integrator->hasRole($roles, $guard);
		return $this->traitHasRole($roles, $guard);
	}
	
	protected function inheritPermissions(): Attribute
	{
		return Attribute::make
		(
			get: fn(bool $value) => $value,
			set: function(bool $value)
			{
				if($value)
					$this->roles()->detach();
				else
					$this->roles()->sync($this->integrator->roles->pluck('id')->toArray());
				return ['inherit_permissions' => $value];
			}
		);
	}
	
	/*****************************************************************
	 * RELATIONSHIPS
	 */
	
	public function integrator(): BelongsTo
	{
		return $this->belongsTo(Integrator::class, 'integrator_id');
	}
	
	public function personalConnections(): BelongsToMany
	{
		return $this->belongsToMany(Person::class, 'integration_connections', 'service_id', 'person_id')
			->wherePivotNotNull('person_id')
			->withPivot('id', 'data', 'enabled', 'className')
			->as('lms_service_connection')
			->using(IntegrationConnection::class);
	}
	
	public function schoolRoles(): BelongsToMany
	{
		if($this->inherit_permissions)
			return $this->integrator->schoolRoles();
		return $this->traitSchoolRoles();
	}
	
	/*****************************************************************
	 * SCOPES
	 */
	#[Scope]
	protected function enabled(Builder $query): void
	{
		$query->where('enabled', true);
	}
	
	
	#[Scope]
	protected function personal(Builder $query): void
	{
		$query->where('can_connect_to_people', true);
	}
	
	#[Scope]
	protected function system(Builder $query): void
	{
		$query->where('can_connect_to_system', true);
	}
	
	#[Scope]
	protected function ofType(Builder $query, IntegratorServiceTypes $type)
	{
		$query->where('service_type', $type);
	}
	
	#[Scope]
	protected function configurable(Builder $query)
	{
		$query->where('configurable', true);
	}
}
