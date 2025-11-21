<?php

namespace App\Models\SubjectMatter\Learning;

use App\Enums\WorkStoragesInstances;
use App\Interfaces\Fileable;
use App\Models\SubjectMatter\Assessment\Skill;
use App\Traits\HasWorkFiles;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningDemonstrationOpportunity extends Model implements Fileable
{
	use HasUuids, HasWorkFiles;
	protected $with = ['demonstrationSession'];
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

	public function getWorkStorageKey(): WorkStoragesInstances
	{
		return WorkStoragesInstances::LearningDemonstrationOpportunityWork;
	}

	public function shouldBePublic(): bool
	{
		return false;
	}
}
