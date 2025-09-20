<?php

namespace App\Models\People;

use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldPermission extends Model
{
	public $timestamps = false;
	public $incrementing = true;
	protected $table = "field_permissions";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'role_id',
			'field',
			'by_self',
			'by_employee',
			'by_students',
			'by_parent',
			'editable',
		];
	
	public function role(): BelongsTo
	{
		return $this->belongsTo(SchoolRoles::class, 'role_id');
	}
	
	protected function casts(): array
	{
		return
			[
				'by_self' => 'boolean',
				'by_employee' => 'boolean',
				'by_students' => 'boolean',
				'by_parent' => 'boolean',
				'editable' => 'boolean',
			];
	}
}
