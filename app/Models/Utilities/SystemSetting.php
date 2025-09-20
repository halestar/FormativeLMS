<?php

namespace App\Models\Utilities;

use App\Traits\UsesJsonValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

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
			$setting = new static();
			$setting->name = static::$settingKey;
			$setting->value = json_encode(static::defaultValue());
			$setting->save();
			$setting->refresh();
		}
		return $setting;
	}
	
	public function workFiles(): MorphToMany|BelongsToMany
	{
		return $this->belongsToMany(WorkFile::class, 'system_files', 'name', 'work_file_id');
	}
	
	public function shouldBePublic(): bool
	{
		return false;
	}
}
