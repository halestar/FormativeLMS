<?php

namespace App\Models\CRUD;

class DismissalReason extends CrudItem
{

    protected $table = 'crud_dismissal_reasons';

    public static function getCrudModel(): string
    {
        return DismissalReason::class;
    }

    public function canDelete(): bool
    {
        return true;
    }

    public static function getCrudModelName(): string
    {
        return __('crud.dismissal_reasons');
    }
}
