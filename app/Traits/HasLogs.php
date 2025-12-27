<?php

namespace App\Traits;

use App\Interfaces\Fileable;
use App\Models\SubjectMatter\Learning\LearningDemonstration;
use App\Models\Utilities\SystemLog;
use App\Enums\SystemLogType;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasLogs
{

	public static function bootHasLogs()
	{
		static::deleting(function(self $model)
		{
			$model->logs()->delete();
		});
	}

	public function logs(): MorphMany
	{
		$relatedFK = ($this->getKeyType() == 'string')? "loggable_uuid": "loggable_id";
		return $this->morphMany(SystemLog::class, 'loggable', 'loggable_type', $relatedFK);
	}

	public function appendSystemLog(SystemLogType $type, string $message): void
	{
		$user = auth()->user();
		$this->logs()->create(
		[
			'type' => $type,
			'message' => $message,
			'posted_by' => $user->id,
			'posted_by_name' => $user->name,
			'posted_by_email' => $user->email,
		]);
	}
}
