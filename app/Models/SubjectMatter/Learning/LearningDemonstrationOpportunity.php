<?php

namespace App\Models\SubjectMatter\Learning;

use App\Enums\WorkStoragesInstances;
use App\Interfaces\Fileable;
use App\Models\People\StudentRecord;
use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\Utilities\SchoolRoles;
use App\Traits\HasWorkFiles;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class LearningDemonstrationOpportunity extends Model implements Fileable
{
	use HasUuids, HasWorkFiles;
	protected $with = ['demonstration'];
	public $timestamps = true;
	public $incrementing = false;
	protected $table = "learning_demonstration_opportunities";
	protected $primaryKey = "id";
	protected $keyType = 'string';
	protected $fillable =
		[
			'posted_on',
			'due_on',
			'completed',
			'submitted_on',
			'feedback',
			'score',
			'criteria_weight',
		];

	protected function casts(): array
	{
		return
			[
				'posted_on' => 'datetime:m/d/Y h:i A',
				'due_on' => 'datetime:m/d/Y h:i A',
				'completed' => 'boolean',
				'submitted_on' => 'datetime:m/d/Y h:i A',
				'score' => 'float',
				'criteria_weight' => 'float',
			];
	}

	public function demonstrationSession(): BelongsTo
	{
		return $this->belongsTo(LearningDemonstrationClassSession::class, 'demonstration_session_id');
	}


	public function skills(): BelongsToMany
	{
		return $this->belongsToMany(Skill::class, 'learning_demonstration_opportunity_assessments',
		                            'opportunity_id', 'skill_id')
		            ->withPivot(['rubric', 'weight', 'id', 'score', 'feedback'])
		            ->as('assessment')
		            ->using(LearningDemonstrationOpportunityAssessment::class);
	}

	public function assessments(): HasMany
	{
		return $this->hasMany(LearningDemonstrationOpportunityAssessment::class, 'opportunity_id');
	}

	public function student(): BelongsTo
	{
		return $this->belongsTo(StudentRecord::class, 'student_id');
	}

	public function getWorkStorageKey(): WorkStoragesInstances
	{
		return WorkStoragesInstances::LearningDemonstrationOpportunityWork;
	}

	public function shouldBePublic(): bool
	{
		return false;
	}

	public function demonstration(): HasOneThrough
	{
		return $this->hasOneThrough(LearningDemonstration::class, LearningDemonstrationClassSession::class, 'id', 'id', 'demonstration_session_id', 'demonstration_id');
	}

	#[Scope]
	protected function posted(Builder $query): void
	{
		$query->where('learning_demonstration_opportunities.posted_on', '<=', now()->format('Y-m-d H:i:s'));
	}

	#[Scope]
	protected function past(Builder $query): void
	{
		$query->where('learning_demonstration_opportunities.due_on', '<', now()->format('Y-m-d H:i:s'));
	}

	#[Scope]
	protected function active(Builder $query): void
	{
		$query->where('learning_demonstration_opportunities.due_on', '>', now()->format('Y-m-d H:i:s'))
			->where('learning_demonstration_opportunities.posted_on', '<=', now()->format('Y-m-d H:i:s'));
	}

	#[Scope]
	protected function completed(Builder $query): void
	{
		$query->where('learning_demonstration_opportunities.completed', true);
	}

	#[Scope]
	protected function submitted(Builder $query): void
	{
		$query->whereNotNull('learning_demonstration_opportunities.submitted_on');
	}
}
