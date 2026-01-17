<?php

namespace App\Models\SubjectMatter\Learning;

use App\Models\Locations\Campus;
use App\Models\Locations\Year;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\SchoolClass;
use Database\Factories\Learning\CriteriaFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

#[UseFactory(CriteriaFactory::class)]
class ClassCriteria extends Model
{
	use HasFactory;
	public $timestamps = false;
	public $incrementing = true;
	protected $table = "class_criteria";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'class_id',
			'name',
			'abbreviation',
		];

	
	public function schoolClass(): BelongsTo
	{
		return $this->belongsTo(SchoolClass::class, 'class_id');
	}
	
	public function sessions(): BelongsToMany
	{
		return $this->belongsToMany(ClassSession::class, 'class_session_criteria', 'criteria_id', 'session_id')
			->withPivot('weight')
			->as('sessionCriteria')
			->using(ClassSessionCriteria::class);
	}

	public function classSessionCriteria(): HasMany
	{
		return $this->hasMany(ClassSessionCriteria::class, 'criteria_id');
	}

	public function learningDemonstrations(): BelongsToMany
	{
		return $this->belongsToMany(LearningDemonstration::class, 'learning_demonstration_class_sessions', 'criteria_id', 'demonstration_id')
			->withPivot(['id', 'session_id', 'criteria_weight', 'posted_on', 'due_on'])
			->using(LearningDemonstrationClassSession::class)
			->as('session');
	}

	public function canDelete(): bool
	{
		return ($this->learningDemonstrations()->count() == 0);
	}
}
