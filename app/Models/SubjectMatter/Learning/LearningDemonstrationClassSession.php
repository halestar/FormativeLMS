<?php

namespace App\Models\SubjectMatter\Learning;

use App\Models\SubjectMatter\ClassSession;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LearningDemonstrationClassSession extends Pivot
{
	use HasUuids;
	public $timestamps = false;
	protected $table = "learning_demonstration_class_sessions";
	public $incrementing = false;
	protected $primaryKey = "id";
	protected $keyType = 'string';
	
	public function demonstration(): BelongsTo
	{
		return $this->belongsTo(LearningDemonstration::class, 'demonstration_id');
	}
	
	public function classSession(): BelongsTo
	{
		return $this->belongsTo(ClassSession::class, 'session_id');
	}

	public function criteria(): BelongsTo
	{
		return $this->belongsTo(ClassCriteria::class, 'criteria_id');
	}
	
	protected function casts()
	{
		return
		[
			'criteria_weight' => 'float',
			'posted_on' => 'datetime:m/d/Y h:i A',
			'due_on' => 'datetime:m/d/Y h:i A',
		];
	}


	
	
}
