<?php

namespace App\Models\CRUD;

use Illuminate\Database\Eloquent\Model;

class Pronouns extends CrudItem
{

    protected $table = 'crud_pronouns';

    public static function getCrudModel(): string
    {
        return Pronouns::class;
    }

    public function canDelete(): bool
    {
        return true;
    }

    public static function getCrudModelName(): string
    {
        return __('crud.pronouns');
    }
}
