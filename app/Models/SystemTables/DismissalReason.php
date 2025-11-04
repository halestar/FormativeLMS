<?php

namespace App\Models\SystemTables;

class DismissalReason extends SystemTableTemplate
{
	public static function getCrudModelName(): string
	{
		return __('crud.dismissal_reasons');
	}
	
	public function canDelete(): bool
	{
		return true;
	}
}
