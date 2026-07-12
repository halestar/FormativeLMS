<?php

namespace App\Models\SystemTables;

use Illuminate\Database\Eloquent\Model;

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
		if ($attributes instanceof \stdClass)
			$attributes = json_decode(json_encode($attributes), true);
		if ($attributes['className'] == static::class)
			return parent::newFromBuilder($attributes, $connection);
		return (new $attributes['className'])->newFromBuilder($attributes, $connection);
	}

	public static function tableModels(): array
	{
		$baseClasses = collect
		([
			Relationship::class,
			Level::class,
			SchoolArea::class,
			DismissalReason::class,
		]);
		$allTableTypes = SystemTable::select('className')
		                            ->groupBy('className')
		                            ->pluck('className');
		return $baseClasses->merge($allTableTypes)->unique()->toArray();
	}

}
