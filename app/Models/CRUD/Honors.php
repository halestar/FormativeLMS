<?php

namespace App\Models\CRUD;

use Illuminate\Database\Eloquent\Model;

class Honors extends CrudItem
{

    protected $table = 'crud_honors';

    public static function getCrudModel(): string
    {
        return Honors::class;
    }

    public function canDelete(): bool
    {
        return true;
    }

    public static function getCrudModelName(): string
    {
        return __('crud.honors');
    }
}
