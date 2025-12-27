<?php

namespace App\Models\SubjectMatter\Learning;

use App\Models\People\StudentRecord;
use App\Models\SubjectMatter\ClassSession;
use Database\Factories\Learning\DemonstrationClassesFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[UseFactory(DemonstrationClassesFactory::class)]
class LearningDemonstrationClassSession extends Pivot
{
	use HasUuids, HasFactory;
	protected $with = ['demonstration'];
	public $timestamps = false;
	protected $table = "learning_demonstration_class_sessions";
	public $incrementing = false;
	protected $primaryKey = "id";
	protected $keyType = 'string';
	protected $guarded = ['id'];
	
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

	public function opportunities(): HasMany
	{
		return $this->hasMany(LearningDemonstrationOpportunity::class, 'demonstration_session_id');
	}

	public function opportunity(StudentRecord|int $student): LearningDemonstrationOpportunity
	{
		return $this->opportunities()->where('student_id', ($student instanceof StudentRecord)? $student->id: $student)->first();
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
