<?php

namespace App\Models\Utilities;

use App\Enums\WorkStoragesInstances;
use App\Interfaces\Fileable;
use App\Models\People\Person;
use App\Traits\HasWorkFiles;
use App\Traits\UsesJsonValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model implements Fileable
{
	// This gives us an easy way to e
	use UsesJsonValue, HasWorkFiles;


	protected static string $settingKey;
	public $incrementing = false;
	public $timestamps = false;
	protected $primaryKey = 'name';
	protected $keyType = 'string';
	protected $table = "system_settings";

	/**
	 * Instance Variables
	 */
	// The instance where this will be saved. Each child instance should have its own
	public static function instance(): static
	{
		return Cache::tags(['system-settings', 'system-settings.' . static::$settingKey])
		            ->rememberForever('system-settings.' . static::$settingKey, function ()
		            {
			            $setting = static::find(static::$settingKey);
			            if (!$setting)
			            {
				            //in this case, there's no data, so make an empty space
				            $setting = new static();
				            $setting->name = static::$settingKey;
				            $setting->value = static::defaultValue();
				            $setting->save();
				            $setting->refresh();
			            }
			            return $setting;
		            });
	}

	protected static function booted(): void
	{
		static::updated(function (SystemSetting $setting)
		{
			Cache::tags('system-settings.' . $setting::$settingKey)->flush();
		});
	}

	/**
	 * Instance Functions
	 */
	protected static function defaultValue(): array
	{
		return [];
	}

	public function workFiles(): MorphMany
	{
		return $this->morphMany(WorkFile::class, 'fileable', 'fileable_type', 'fileable_uuid');
	}

	public function shouldBePublic(): bool
	{
		return false;
	}

	protected function casts(): array
	{
		return [
			'value' => 'array',
		];
	}

	public function getWorkStorageKey(): WorkStoragesInstances
	{
		return WorkStoragesInstances::SystemFiles;
	}

	public function canAccessFile(Person $person, WorkFile $file): bool
	{
		return true;
	}
}
