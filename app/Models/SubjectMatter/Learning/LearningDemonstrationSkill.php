<?php

namespace App\Models\SubjectMatter\Learning;

use App\Casts\Learning\Rubric;
use App\Interfaces\HasRubric;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LearningDemonstrationSkill extends Pivot implements HasRubric
{
	use HasUuids;
	public $timestamps = false;
	protected $table = "learning_demonstration_skills";
	public $incrementing = false;
	protected $primaryKey = "id";
	protected $keyType = 'string';
	
	public function demonstration(): BelongsTo
	{
		return $this->belongsTo(LearningDemonstration::class, 'demonstration_id');
	}

	public function skill(): BelongsTo
	{
		return $this->belongsTo($this->skillable_type, 'skill_id');
	}

	protected function casts()
	{
		return
			[
				'rubric' => Rubric::class,
				'weight' => 'float',
			];
	}
	
	public function getRubric(): ?Rubric
	{
		return $this->rubric;
	}
	
	public function setRubric(Rubric $rubric)
	{
		$this->rubric = $rubric;
	}
	
	public function getDescription(): string
	{
		return $this->skill->description;
	}
	
	public function getSkillId(): int
	{
		return $this->skill->id;
	}
	
	public function getSkillName(): string
	{
		return $this->skill->name;
	}
}
