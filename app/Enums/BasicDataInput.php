<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum BasicDataInput: string
{
    use EnumToArray;
	case TEXT = 'text';
	case NUMBER = 'number';
	case FLOAT = 'float';
	case MULTIPLE_SELECTIONS = 'checkbox';
	case CHOICE = 'radio';
	case COMBO = 'select';

	public function label(): string
	{
		return match ($this)
		{
			self::TEXT => __('ai.fields.types.text'),
			self::NUMBER => __('ai.fields.types.number'),
			self::FLOAT => __('ai.fields.types.float'),
			self::MULTIPLE_SELECTIONS => __('ai.fields.types.checkboxes'),
			self::CHOICE => __('ai.fields.types.choice'),
			self::COMBO => __('ai.fields.types.combo'),
		};
	}

	public function isValid(mixed $value, array $options = []): bool
	{
		return match ($this)
		{
			self::TEXT => is_string($value),
			self::NUMBER => is_numeric($value),
			self::FLOAT => is_float($value),
			self::MULTIPLE_SELECTIONS => is_array($value) && is_array($options) && count($options)> 0 &&
				array_reduce($value, fn($item, $carry) => $carry && isset($options[$item]), true),
			self::CHOICE, self::COMBO => is_array($options) && count($options) > 0 && isset($options[$value]),

		};
	}
}
