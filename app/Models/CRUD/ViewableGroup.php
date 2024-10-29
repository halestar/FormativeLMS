<?php

namespace App\Models\CRUD;

use App\Models\People\ViewPolicies\ViewableField;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ViewableGroup extends CrudItem
{

    protected $table = 'crud_viewable_groups';
    public const HIDDEN = 1;
    public const BASIC_INFO = 2;
    public const CONTACT_INFO = 3;
    public const RELATIONSHIPS = 4;


    public static function getCrudModel(): string
    {
        return ViewableGroup::class;
    }

    public function canDelete(): bool
    {
        return false;
    }

    public static function getCrudModelName(): string
    {
        return __('crud.view_categories');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(ViewableField::class, 'group_id');
    }


}
