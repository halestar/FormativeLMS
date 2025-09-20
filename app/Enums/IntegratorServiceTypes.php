<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum IntegratorServiceTypes: string
{
	use EnumToArray;
	
	case AUTHENTICATION = 'auth';
	case DOCUMENTS = 'documents';
	case WORK = 'work';
	case AI = 'ai';
	case SMS = 'sms';
	
	public function label(): string
	{
		return match ($this)
		{
			self::AUTHENTICATION => __('integrators.services.auth'),
			self::DOCUMENTS => __('integrators.services.documents'),
			self::WORK => __('integrators.services.work'),
			self::AI => __('integrators.services.ai'),
			self::SMS => __('integrators.services.sms'),
		};
		
	}
}
