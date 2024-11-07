<?php

namespace App\Models\CRUD;

use App\Models\Locations\Campus;
use App\Models\People\ViewPolicies\ViewableField;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Level extends CrudItem
{

    protected $table = 'crud_levels';


    public static function getCrudModel(): string
    {
        return Level::class;
    }

    public function canDelete(): bool
    {
        return false;
    }

    public static function getCrudModelName(): string
    {
        return trans_choice('crud.level', 2);
    }

    public function campuses(): BelongsToMany
    {
        return $this->belongsToMany(Campus::class, 'crud_level_campuses', 'level_id', 'campus_id');
    }
}

