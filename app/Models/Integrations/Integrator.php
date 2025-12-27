<?php

namespace App\Models\Integrations;

use App\Casts\Utilities\AsJsonData;
use App\Interfaces\HasSchoolRoles;
use App\Models\Scopes\OrderByNameScope;
use App\Traits\HasSchoolRolesTrait;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Integrator extends Model implements HasSchoolRoles
{
	/*************************************************
	 * PROPERTIES
	 */
	use HasSchoolRolesTrait;
	
	final public const INTEGRATOR_URL_PREFIX = '/integrators';
	final public const INTEGRATOR_ACTION_PREFIX = 'integrators.';
	public $timestamps = true;
	public $incrementing = true;
	protected $table = "integrators";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'name',
			'className',
			'path',
			'description',
			'version',
			'configurable',
		];
	protected $guard_name = 'web';
	
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

	protected static function booted(): void
	{
		static::addGlobalScope(new OrderByNameScope);
	}
	
	/*****************************************************************
	 * RELATIONSHIPS
	 */
	
	public function services(): HasMany
	{
		return $this->hasMany(IntegrationService::class, 'integrator_id');
	}
	
	protected function casts(): array
	{
		return
			[
				'data' => AsJsonData::class,
				'version' => 'string',
				'enabled' => 'boolean',
				'has_personal_connections' => 'boolean',
				'has_system_connections' => 'boolean',
				'configurable' => 'boolean',
				'created_at' => 'datetime: m/d/Y h:i A',
				'updated_at' => 'datetime: m/d/Y h:i A',
			];
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
		$query->where('has_personal_connections', true);
	}
	
	#[Scope]
	protected function system(Builder $query): void
	{
		$query->where('has_system_connections', true);
	}
	
	#[Scope]
	protected function configurable(Builder $query): void
	{
		$query->where('configurable', true);
	}
}
