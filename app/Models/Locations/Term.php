<?php

namespace App\Models\Locations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Term extends Model
{
	public $timestamps = true;
	public $incrementing = true;
	protected $table = "terms";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'campus_id',
			'year_id',
			'label',
			'term_start',
			'term_end',
		];
	
	public static function currentTerm(Campus $campus): ?Term
	{
		return Cache::rememberForever('current-term-' . $campus->id, function() use ($campus)
		{
			$now = date('Y-m-d');
			$term = Term::whereDate('term_start', '<=', $now)
			            ->whereDate('term_end', '>=', $now)
			            ->where('campus_id', $campus->id)
			            ->first();
			//if not, return the latest term
			if(!$term)
				$term = Term::where('campus_id', $campus->id)
				            ->orderByDesc('term_end')
				            ->first();
			return $term;
		});
	}
	
	public static function currentTerms(): Collection
	{
		return Cache::rememberForever('current-terms', function()
		{
			$now = date('Y-m-d');
			return Term::whereDate('term_start', '<=', $now)
			           ->whereDate('term_end', '>=', $now)
			           ->get();
		});
	}
	
	protected static function booted(): void
	{
		static::addGlobalScope('term-start-order', function(Builder $builder)
		{
			$builder->orderBy('term_start');
		});
	}
	
	public function canDelete(): bool
	{
		return true;
	}
	
	public function year(): BelongsTo
	{
		return $this->belongsTo(Year::class, 'year_id');
	}
	
	public function campus(): BelongsTo
	{
		return $this->belongsTo(Campus::class, 'campus_id');
	}
	
	public function isCurrent(): bool
	{
		$now = date('Y-m-d');
		return $this->term_start <= $now && $this->term_end >= $now;
	}
	
	protected function casts(): array
	{
		return
			[
				'term_start' => 'date: ' . config('lms.date_format'),
				'term_end' => 'date: ' . config('lms.date_format'),
			];
	}
}
