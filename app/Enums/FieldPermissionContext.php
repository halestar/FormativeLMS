<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum FieldPermissionContext: string
{
	use EnumToArray;

	case SELF = 'self';
	case OTHER = 'other';
	case CHILD = 'child';
	case ROSTER = 'roster';
}
