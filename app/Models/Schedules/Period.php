<?php

namespace App\Models\Schedules;

use App\Classes\Days;
use App\Models\Locations\Campus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Period extends Model
{
	public $timestamps = true;
	public $incrementing = true;
	protected $table = "periods";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'campus_id',
			'day',
			'name',
			'abbr',
			'start',
			'end',
			'active',
		];
	
	protected static function booted(): void
	{
		static::addGlobalScope('period-order', function(Builder $builder)
		{
			$builder->orderBy('day', 'asc')
			        ->orderBy('start', 'asc')
			        ->orderBy('end', 'asc');
		});
	}
	
	public function canDelete(): bool
	{
		return true;
	}
	
	public function campus(): BelongsTo
	{
		return $this->belongsTo(Campus::class, 'campus_id');
	}
	
	public function dayStr()
	{
		return Days::day($this->day);
	}
	
	public function scopeActive(Builder $builder)
	{
		$builder->where('active', true);
	}
	
	public function blocks(): BelongsToMany
	{
		return $this->belongsToMany(Block::class, 'blocks_periods', 'period_id', 'block_id');
	}
	
	public function __toString(): string
	{
		return $this->abbr;
	}
	
	protected function casts(): array
	{
		return
			[
				'day' => 'integer',
				'start' => 'datetime: ' . config('lms.time_format'),
				'end' => 'datetime: ' . config('lms.time_format'),
				'active' => 'boolean',
			];
	}
	
}
