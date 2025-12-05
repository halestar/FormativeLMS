<?php

namespace App\Models\SystemTables;

use App\Models\Scopes\OrderByOrderScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;

#[ScopedBy([OrderByOrderScope::class])]
class SystemTable extends Model
{
	public $timestamps = false;
	protected $guarded = ['id'];
	protected $fillable = ['name', 'order', 'className'];
	protected $table = 'system_tables';
	protected $primaryKey = 'id';
	public $incrementing = true;
	
	public function __toString(): string
	{
		return $this->name;
	}
	
	public function newFromBuilder($attributes = [], $connection = null)
	{
		if($attributes instanceof \stdClass)
			$attributes = json_decode(json_encode($attributes), true);
		if($attributes['className'] == static::class)
			return parent::newFromBuilder($attributes, $connection);
		return (new $attributes['className'])->newFromBuilder($attributes, $connection);
	}
	
	public static function tableModels(): array
	{
		return SystemTable::groupBy('className')
							->get()
							->sortBy(fn($model) => $model::getCrudModelName())
		                    ->pluck('className')
		                    ->toArray();
	}

}
