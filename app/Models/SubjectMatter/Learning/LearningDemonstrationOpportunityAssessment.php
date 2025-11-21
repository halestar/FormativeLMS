<?php

namespace App\Models\SubjectMatter\Learning;

use App\Casts\Learning\RubricAssessment;
use App\Models\SubjectMatter\Assessment\Skill;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningDemonstrationOpportunityAssessment extends Model
{
	use HasUuids;
	public $timestamps = false;
	public $incrementing = false;
	protected $table = "learning_demonstration_opportunity_assessments";
	protected $primaryKey = "id";
	protected $keyType = 'string';
	protected $fillable =
		[
			'weight',
			'score',
			'feedback',
		];

	protected function casts(): array
	{
		return
			[
				'rubric' => RubricAssessment::class,
				'score' => 'float',
				'weight' => 'float',
			];
	}

	public function opportunity(): BelongsTo
	{
		return $this->belongsTo(LearningDemonstrationOpportunity::class, 'opportunity_id');
	}

	public function skill(): BelongsTo
	{
		return $this->belongsTo(Skill::class, 'skill_id');
	}
}
