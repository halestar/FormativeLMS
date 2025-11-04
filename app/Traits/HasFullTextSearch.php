<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait HasFullTextSearch
{
	protected static function searchFields(): array
	{
		return Cache::rememberForever('searchFields_' . with(new static)->getTable(), function()
		{
			return collect(DB::select("SHOW INDEXES FROM " . with(new static)->getTable() . " where Index_type='FULLTEXT'"))
				->pluck('Column_name')
				->toArray();
		});
	}
	
	#[Scope]
	protected function search(Builder $query, string $searchTerm): void
	{
		$query->whereFullText(static::searchFields(), $searchTerm, ['mode' => 'boolean'])
			->addSelect(DB::raw("*, MATCH ( " . implode(",", static::searchFields()) .
				") AGAINST (?) as score"))
			->addBinding($searchTerm, "select")
			->orderByDesc('score');
	}
}
