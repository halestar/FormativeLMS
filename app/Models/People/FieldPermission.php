<?php

namespace App\Models\People;

use App\Enums\FieldPermissionAction;
use App\Enums\FieldPermissionContext;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldPermission extends Model
{
	public $timestamps = true;
	public $incrementing = true;
	protected $table = "field_permissions";
	protected $primaryKey = "id";
	protected $guarded =
		[
			'id',
		];

	public function viewerRole(): BelongsTo
	{
		return $this->belongsTo(SchoolRoles::class, 'viewer_role_id');
	}

	public function targetRole(): BelongsTo
	{
		return $this->belongsTo(SchoolRoles::class, 'target_role_id');
	}

	protected function casts(): array
	{
		return
			[
				'context' => FieldPermissionContext::class,
				'action'  => FieldPermissionAction::class,
				'allow'   => 'boolean',
			];
	}
}
