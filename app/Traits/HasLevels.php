<?php

namespace App\Traits;

use App\Models\SystemTables\Level;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasLevels
{
	public function levels(): MorphToMany
	{
		return $this->morphToMany(Level::class, 'system_tableable', 'system_tableable',
					'system_tableable_id', 'system_table_id')
		            ->orderBy('order');
	}
}
