<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum WorkStoragesInstances: string
{
	use EnumToArray;
	
	case StudentWork = 'student_work';
	case EmployeeWork = 'employee_work';
	case ClassWork = 'class_work';
	case EmailWork = 'email_work';
	case AiWork = 'ai_work';
	case LearningDemonstrationWork = 'ld_work';
	case LearningDemonstrationOpportunityWork = 'ldo_work';
	case TempWork = 'temp_work';
	
	public function label(): string
	{
		return match ($this)
		{
			self::StudentWork => __('integrators.work.student_work'),
			self::EmployeeWork => __('integrators.work.employee_work'),
			self::ClassWork => __('integrators.work.class_work'),
			self::EmailWork => __('integrators.work.email_work'),
			self::AiWork => __('integrators.work.ai_work'),
			self::LearningDemonstrationWork => __('integrators.work.ld_work'),
			self::LearningDemonstrationOpportunityWork => __('integrators.work.ldo_work'),
			self::TempWork => __('integrators.work.temp'),
		};
		
	}
}
