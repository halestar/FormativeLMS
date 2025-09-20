<?php

namespace App\Models\Integrations;

use App\Casts\Utilities\AsJsonData;
use App\Enums\IntegratorServiceTypes;
use App\Models\People\Person;
use App\Traits\UsesJsonValue;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\Pivot;

class IntegrationConnection extends Pivot
{
	use HasUuids;
	
	/*************************************************
	 * PROPERTIES
	 */
	use UsesJsonValue;
	
	public $timestamps = false;
	public $incrementing = false;
	protected $table = "integration_connections";
	protected $primaryKey = "id";
	protected $keyType = 'string';
	
	public function isSystemConnection(): bool
	{
		return ($this->person_id == null);
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
	
	/*****************************************************************
	 * RELATIONSHIPS
	 */
	
	public function person(): BelongsTo
	{
		return $this->belongsTo(Person::class, 'person_id');
	}
	
	public function service(): BelongsTo
	{
		return $this->belongsTo(IntegrationService::class, 'service_id');
	}
	
	public function integrator(): HasOneThrough
	{
		return $this->hasOneThrough(Integrator::class,
			IntegrationService::class,
			'id', 'id', 'service_id', 'integrator_id');
	}
	
	protected function casts(): array
	{
		return
			[
				'data' => AsJsonData::class,
				'enabled' => 'boolean',
			];
	}
	
	/*****************************************************************
	 * SCOPES
	 */
	#[Scope]
	protected function enabled(Builder $query): void
	{
		$query->where('integration_connections.enabled', true);
	}
	
	#[Scope]
	protected function system(Builder $query): void
	{
		$query->whereNull('integration_connections.person_id');
	}
	
	#[Scope]
	protected function personal(Builder $query): void
	{
		$query->whereNotNull('integration_connections.person_id');
	}
	
	#[Scope]
	protected function ofType(Builder $query, IntegratorServiceTypes $type): void
	{
		$query->join('integration_services', 'integration_services.id', '=', 'integration_connections.service_id')
		      ->where('integration_services.service_type', $type);
	}
}