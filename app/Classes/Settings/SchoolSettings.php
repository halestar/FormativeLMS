<?php

namespace App\Classes\Settings;

use App\Casts\Utilities\SystemSettings\SchoolNames;
use App\Models\Integrations\IntegrationConnection;
use App\Models\Integrations\IntegrationService;
use App\Models\Utilities\SystemSetting;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SchoolSettings extends SystemSetting
{
	public const int TERM = 1;
	public const int YEAR = 2;
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
                "rubrics_max_points" => 5,
				"force_class_management" => true,
				"class_management_service_id" => null,
                "terms_of_service" => '',
                "privacy_policy" => '',
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

    public function rubricsMaxPoints(): Attribute
    {
        return $this->basicProperty('rubrics_max_points');
    }

	public function forceClassManagement(): Attribute
	{
		return $this->basicProperty('force_class_management');
	}

	public function classManagementServiceId(): Attribute
	{
		return $this->basicProperty('class_management_service_id');
	}

	public function classManagementService(): Attribute
	{
		return Attribute::make
		(
			get: function(mixed $value, array $attributes)
			{
				$serviceId = $this->getValue($attributes['value'], 'class_management_service_id', null);
				if(!$serviceId) return null;
				return IntegrationService::find($serviceId);
			},
		);
	}
	
	public function maxMsg(): Attribute
	{
		return Attribute::make
		(
			get: fn(mixed $value, array $attributes) => $this->getValue($attributes['value'], 'max_msg', "10"),
			set: fn(mixed $value, array $attributes) => $this->updateValue($attributes['value'], 'max_msg', $value),
		);
	}


	
	public function yearMessages(): Attribute
	{
		return Attribute::make
		(
			get: fn(mixed $value, array $attributes) => $this->getValue($attributes['value'], 'year_msg', self::YEAR),
			set: fn(int $value, array $attributes) => $this->updateValue($attributes['value'], 'year_msg', $value),
		);
	}

    public function termsOfService(): Attribute
    {
        return $this->basicProperty('terms_of_service');
    }

    public function privacyPolicy(): Attribute
    {
        return $this->basicProperty('privacy_policy');
    }
	
	protected function casts(): array
	{
		return [
			'studentName' => SchoolNames::class,
			'employeeName' => SchoolNames::class,
			'parentName' => SchoolNames::class,
		];
	}
}
