<?php

namespace App\Models\CRUD;

use App\Models\People\PersonalRelations;

class Relationship extends CrudItem
{
    protected $table = 'crud_relationships';
    protected $fillable = ['relationship_id'];

    public const PARENT = 1;
    public const STEPPARENT = 2;
    public const GUARDIAN = 3;
    public const CHILD = 4;
    public const SPOUSE = 5;
    public const GRANDPARENT = 6;

    public static function getCrudModel(): string
    {
        return Relationship::class;
    }

    public static function getCrudModelName(): string
    {
        return __('crud.relationships');
    }

    public function canDelete(): bool
    {
        return PersonalRelations::where('relationship_id', $this->id)->count() == 0;
    }

    public static function parentalRelationshipTypes(): array
    {
        return [
            self::PARENT,
            self::STEPPARENT,
            self::GUARDIAN,
            self::GRANDPARENT
        ];
    }
}
