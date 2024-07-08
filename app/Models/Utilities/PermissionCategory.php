<?php

namespace App\Models\Utilities;

use App\Casts\LogItem;
use App\Models\Scopes\OrdeByNameScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Permission;

#[ScopedBy([OrdeByNameScope::class])]
class PermissionCategory extends Model
{
    public $timestamps = true;
    protected $table = "permission_categories";
    protected $primaryKey = "id";
    public $incrementing = true;
    protected $guarded = ['id'];
    protected $casts =
        [
            'created_at' => 'datetime: m/d/Y h:i A',
            'updated_at' => 'datetime: m/d/Y h:i A',
        ];
    public function permissions(): HasMany
    {
        return $this->hasMany(SchoolPermission::class, 'category_id');
    }
}
