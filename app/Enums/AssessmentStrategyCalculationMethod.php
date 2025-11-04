<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum AssessmentStrategyCalculationMethod: string
{
	use EnumToArray;
	case Percent = 'percent';
}
