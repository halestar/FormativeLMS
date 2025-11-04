<?php

namespace App\Models\SystemTables;

use App\Models\People\PersonalRelations;

class Relationship extends SystemTableTemplate
{
	public const PARENT = 1;
	public const STEPPARENT = 2;
	public const GUARDIAN = 3;
	public const CHILD = 4;
	public const SPOUSE = 5;
	public const GRANDPARENT = 6;
	
	
	public static function getCrudModelName(): string
	{
		return __('crud.relationships');
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
	
	public function canDelete(): bool
	{
		return PersonalRelations::where('relationship_id', $this->id)
		                        ->count() == 0;
	}
}
