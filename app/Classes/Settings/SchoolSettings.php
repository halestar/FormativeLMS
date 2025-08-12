<?php

namespace App\Classes\Settings;

use App\Casts\Utilities\SystemSettings\SchoolNames;
use App\Classes\Days;
use App\Models\Utilities\SystemSetting;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SchoolSettings extends SystemSetting
{
    public const TERM = 1;
    public const YEAR = 2;

	protected function casts(): array
	{
		return [
			'studentName' => SchoolNames::class,
			'employeeName' => SchoolNames::class,
			'parentName' => SchoolNames::class,
		];
	}
	protected static string $settingKey = "school";

	protected static function defaultValue(): array
	{
		return
			[
				"days" => Days::weekdaysOptions(),
				"start" => "08:00",
				"end" => "16:00",
				"studentName" => [],
				"employeeName" => [],
				"parentName" => [],
			];
	}

    public function days(): Attribute
    {
	    return $this->basicProperty('days');
    }

    public function startTime(): Attribute
    {
	    return $this->basicProperty('start');
    }

    public function endTime(): Attribute
    {
	    return $this->basicProperty('end');
    }

    public function maxMsg(): Attribute
    {
        return Attribute::make
        (
            get: fn(mixed $value, array $attributes) =>
                $this->getValue($attributes['value'], 'max_msg', "10"),
            set: fn(mixed $value, array $attributes) =>
                $this->updateValue($attributes['value'], 'max_msg', $value),
        );
    }

    public function yearMessages(): Attribute
    {
        return Attribute::make
        (
            get: fn(mixed $value, array $attributes) =>
                $this->getValue($attributes['value'], 'year_msg', self::YEAR),
            set: fn(int $value, array $attributes) =>
                $this->updateValue($attributes['value'], 'year_msg', $value),
        );
    }
}
