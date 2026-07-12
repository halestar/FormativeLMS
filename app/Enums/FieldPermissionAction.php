<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum FieldPermissionAction: string
{
	use EnumToArray;

	case VIEW = 'view';
	case EDIT = 'edit';
	case DELETE = 'delete';
	case EXPORT = 'export';
}
