<?php

namespace App\Models\Schedules;

use App\Models\Locations\Campus;
use App\Models\Scopes\OrderByOrderScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ScopedBy(OrderByOrderScope::class)]
class Block extends Model
{
	public $timestamps = true;
	public $incrementing = true;
	protected $table = "blocks";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'campus_id',
			'name',
			'order',
			'active',
		];
	
	public function canDelete(): bool
	{
		return true;
	}
	
	public function campus(): BelongsTo
	{
		return $this->belongsTo(Campus::class, 'campus_id');
	}
	
	public function periods(): BelongsToMany
	{
		return $this->belongsToMany(Period::class, 'blocks_periods', 'block_id', 'period_id');
	}
	
	public function scopeActive(Builder $builder)
	{
		$builder->where('active', true);
	}
	
	protected function casts(): array
	{
		return
			[
				'order' => 'integer',
				'active' => 'boolean',
			];
	}
}
