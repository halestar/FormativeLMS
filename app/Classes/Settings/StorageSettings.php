<?php

namespace App\Classes\Settings;

use App\Enums\WorkStoragesInstances;
use App\Models\Integrations\Connections\WorkFilesConnection;
use App\Models\Integrations\IntegrationConnection;
use App\Models\Utilities\SystemSetting;
use Illuminate\Database\Eloquent\Casts\Attribute;

class StorageSettings extends SystemSetting
{
	protected static string $settingKey = "storage";
	
	protected static function defaultValue(): array
	{
		$vals = [];
		foreach(WorkStoragesInstances::values() as $val)
			$vals[$val] = null;
		return ['work' => $vals];
	}
	
	public function workStorages(): Attribute
	{
		return $this->basicProperty('work');
	}
	
	public function getWorkConnection(WorkStoragesInstances $instance): ?WorkFilesConnection
	{
		return IntegrationConnection::find($this->work_storages[$instance->value]);
	}
	
	protected function casts(): array
	{
		return [];
	}
}