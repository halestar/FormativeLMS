<?php

namespace App\Models\Utilities;

use App\Models\Scopes\OrderByNameScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy([OrderByNameScope::class])]
class PermissionCategory extends Model
{
	public $timestamps = true;
	public $incrementing = true;
	protected $table = "permission_categories";
	protected $primaryKey = "id";
	protected $guarded = ['id'];
	protected $casts =
		[
			'created_at' => 'datetime: m/d/Y h:i A',
			'updated_at' => 'datetime: m/d/Y h:i A',
		];
	
	public function permissions(): HasMany
	{
		return $this->hasMany(SchoolPermission::class, 'category_id')
		            ->orderBy('name');
	}
}
