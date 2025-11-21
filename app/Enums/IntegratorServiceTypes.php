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
	case EMAIL = 'email';
	case CLASSES = 'classes';
	
	public function label(): string
	{
		return match ($this)
		{
			self::AUTHENTICATION => __('integrators.services.auth'),
			self::DOCUMENTS => __('integrators.services.documents'),
			self::WORK => __('integrators.services.work'),
			self::AI => __('integrators.services.ai'),
			self::SMS => __('integrators.services.sms'),
			self::EMAIL => __('integrators.services.email'),
			self::CLASSES => __('integrators.services.classes'),
		};
	}

	public function icons(): string
	{
		return match($this)
		{
			self::AUTHENTICATION => "images/auth_service.svg",
			self::DOCUMENTS => "images/documents_service.svg",
			self::WORK => "images/work_service.svg",
			self::AI => "images/ai-icon.svg",
			self::SMS => "images/sms_service.svg",
			self::EMAIL => "images/email_service.svg",
			self::CLASSES => "images/classes_service.svg",
		};
	}
}
