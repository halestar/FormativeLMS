<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum SystemLogType: string
{
	use EnumToArray;
    case LearningDemonstration = "learning-demonstration";
	case Person = "person";
	case Student = "student";

	public function label(): string
	{
		return match ($this)
		{
			self::LearningDemonstration => trans_choice('learning.demonstrations', 2),
			self::Person => __('people.id.person'),
			self::Student => __('common.student'),
		};
	}
}
