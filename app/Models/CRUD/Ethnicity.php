<?php

namespace App\Models\CRUD;

use Illuminate\Database\Eloquent\Model;

class Ethnicity extends CrudItem
{

    protected $table = 'crud_ethnicities';

    public static function getCrudModel(): string
    {
        return Ethnicity::class;
    }

    public function canDelete(): bool
    {
        return true;
    }

    public static function getCrudModelName(): string
    {
        return __('crud.ethnicities');
    }
}
