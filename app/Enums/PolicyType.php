<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum PolicyType: string
{
	use EnumToArray;
	
	case EMPLOYEE = 'Employee';
	case STUDENT = 'Student';
	case PARENT = 'Parent';
}
