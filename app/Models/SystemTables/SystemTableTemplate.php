<?php

namespace App\Models\SystemTables;

use App\Models\Scopes\FilterClassNameScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Support\Collection;

#[ScopedBy([FilterClassNameScope::class])]
abstract class SystemTableTemplate extends SystemTable
{
	public static function all($columns = ['*']): Collection
	{
		return self::where('className', static::class)->orderBy('order')->get();
	}

    public static function asOptionsArray(): array
    {
        return static::all()->mapWithKeys(fn($model) => [$model->id => $model->name])->toArray();
    }
	abstract public static function getCrudModelName(): string;
	abstract public function canDelete(): bool;
}
