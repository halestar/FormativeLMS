<?php

namespace App\Models\CRUD;

use Illuminate\Database\Eloquent\Model;

class Suffix extends CrudItem
{

    protected $table = 'crud_suffixes';

    public static function getCrudModel(): string
    {
        return Suffix::class;
    }

    public function canDelete(): bool
    {
        return true;
    }

    public static function getCrudModelName(): string
    {
        return __('crud.suffixes');
    }
}
