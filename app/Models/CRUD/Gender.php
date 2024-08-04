<?php

namespace App\Models\CRUD;

use Illuminate\Database\Eloquent\Model;

class Gender extends CrudItem
{

    protected $table = 'crud_gender';

    public static function getCrudModel(): string
    {
        return Gender::class;
    }

    public function canDelete(): bool
    {
        return true;
    }

    public static function getCrudModelName(): string
    {
        return __('crud.gender');
    }
}
