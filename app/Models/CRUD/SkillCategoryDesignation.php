<?php

namespace App\Models\CRUD;

class SkillCategoryDesignation extends CrudItem
{
    protected $table = 'crud_skill_category_designations';

    public static function getCrudModel(): string
    {
        return SkillCategoryDesignation::class;
    }

    public static function getCrudModelName(): string
    {
        return __('crud.skill_category_designation');
    }

    public function canDelete(): bool
    {
        return true;
    }


}
