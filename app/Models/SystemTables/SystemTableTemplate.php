<?php

namespace App\Models\SystemTables;

use App\Models\Scopes\FilterClassNameScope;
use App\Models\Scopes\OrderByOrderScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;


abstract class SystemTableTemplate extends SystemTable
{
	public static function all($columns = ['*']): Collection
	{
		return self::where('className', static::class)->orderBy('order')->get();
	}

	protected static function booted(): void
	{
		static::addGlobalScopes([new FilterClassNameScope, new OrderByOrderScope]);
	}

    public static function asOptionsArray(): array
    {
        return static::all()->mapWithKeys(fn($model) => [$model->id => $model->name])->toArray();
    }
	abstract public static function getCrudModelName(): string;
	abstract public function canDelete(): bool;
}
