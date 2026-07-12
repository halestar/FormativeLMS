<?php

namespace App\Models\People;

use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PersonRole extends MorphPivot
{
	public $timestamps = false;
	public $incrementing = false;
	protected $table = "model_has_roles";
	protected $primaryKey = null;

	protected $guarded = [];

	protected function casts(): array
	{
		return ['field_values' => 'array'];
	}

	public function role(): BelongsTo
	{
		return $this->belongsTo(SchoolRoles::class, 'role_id');
	}

	public function model(): MorphTo
	{
		return $this->morphTo();
	}

}
