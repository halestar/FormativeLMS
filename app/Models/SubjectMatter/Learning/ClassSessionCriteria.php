<?php

namespace App\Models\SubjectMatter\Learning;

use App\Models\SubjectMatter\ClassSession;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClassSessionCriteria extends Pivot
{
    public $timestamps = false;
	protected $table = "class_session_criteria";
	
	protected function casts(): array
	{
		return
			[
				'weight' => 'float',
			];
	}
	
	public function classSession(): BelongsTo
	{
		return $this->belongsTo(ClassSession::class, 'session_id');
	}
	
	public function criteria(): BelongsTo
	{
		return $this->belongsTo(ClassCriteria::class, 'criteria_id');
	}
}
