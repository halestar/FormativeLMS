<?php

namespace App\Models\SystemTables;

use App\Models\Locations\Campus;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Level extends SystemTableTemplate
{
	
	public static function getCrudModelName(): string
	{
		return trans_choice('crud.level', 2);
	}
	
	public function canDelete(): bool
	{
		return false;
	}
	
	public function campuses(): MorphToMany
	{
		return $this->morphedByMany(Campus::class, 'system_tableable', 'system_tableable', 'system_table_id', 'system_tableable_id');
	}
}

