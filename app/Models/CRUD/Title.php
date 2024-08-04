<?php

namespace App\Models\CRUD;

use Illuminate\Database\Eloquent\Model;

class Title extends CrudItem
{

    protected $table = 'crud_titles';

    public static function getCrudModel(): string
    {
        return Title::class;
    }

    public function canDelete(): bool
    {
        return true;
    }

    public static function getCrudModelName(): string
    {
        return __('crud.title');
    }
}
