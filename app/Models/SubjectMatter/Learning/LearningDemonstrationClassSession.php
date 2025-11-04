<?php

namespace App\Models\SubjectMatter\Learning;

use App\Models\SubjectMatter\ClassSession;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LearningDemonstrationClassSession extends Pivot
{
	public $timestamps = false;
	protected $table = "learning_demonstration_class_session";
	
	public function demonstration(): BelongsTo
	{
		return $this->belongsTo(LearningDemonstration::class, 'demonstration_id');
	}
	
	public function classSession(): BelongsTo
	{
		return $this->belongsTo(ClassSession::class, 'session_id');
	}
	
	protected function casts()
	{
		return
			[
				'posted_on' => 'datetime:m/d/Y h:i A',
				'due_on' => 'datetime:m/d/Y h:i A',
			];
	}
	
	
}
