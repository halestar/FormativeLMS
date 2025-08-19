<?php

namespace App\Models\Utilities;

use App\Traits\UsesJsonValue;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
	// This gives us an easy way to e
	use UsesJsonValue;

    protected $primaryKey = 'name';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
	protected $table = "system_settings";

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

	/**
	 * Instance Variables
	 */
	// The instance where this will be saved. Each child instance should have its own
	protected static string $settingKey;

	/**
	 * Instance Functions
	 */
	protected static function defaultValue(): array
	{
		return [];
	}

	public static function instance(): static
	{
		$setting = static::find(static::$settingKey);
		if(!$setting)
		{
			//in this case, there's no data, so make an empty space
			$setting = new self();
			$setting->name = static::$settingKey;
			$setting->value = static::defaultValue();
			$setting->save();
		}
		return $setting;
	}

	protected function basicProperty($propertyName = null): Attribute
	{
		return Attribute::make
		(
			get: fn(mixed $value, array $attributes) =>
				$this->getValue($attributes['value'], $propertyName, (static::defaultValue())[$propertyName]),
			set: fn(mixed $value, array $attributes) =>
				$this->updateValue($attributes['value'], $propertyName, $value),
		);
	}
}
