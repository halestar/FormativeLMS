<?php

namespace App\Models\Utilities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Permission;

class SchoolPermission extends Permission
{
    public function category(): BelongsTo
    {
        return $this->belongsTo(PermissionCategory::class, 'category_id');
    }
}
