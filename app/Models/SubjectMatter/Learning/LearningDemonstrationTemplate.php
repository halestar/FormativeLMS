<?php

namespace App\Models\SubjectMatter\Learning;

use App\Casts\Learning\AsDemonstrationQuestions;
use App\Casts\Learning\AsUrlResources;
use App\Casts\Learning\LearningDemonstrationRubrics;
use App\Enums\WorkStoragesInstances;
use App\Interfaces\Fileable;
use App\Models\People\Person;
use App\Models\SubjectMatter\Assessment\CharacterSkill;
use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\SubjectMatter\Course;
use App\Models\Utilities\WorkFile;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class LearningDemonstrationTemplate extends Model implements Fileable
{
	use HasUuids;
	public $timestamps = true;
	public $incrementing = false;
	protected $table = "learning_demonstration_templates";
	protected $primaryKey = "id";
	protected $keyType = 'string';
	protected $fillable =
		[
			'name',
			'abbr',
			'demonstration',
			'allow_rating',
			'online_submission',
			'open_submission',
			'submit_after_due',
			'share_submissions',
			'shareable',
		];
	
	protected function casts(): array
	{
		return
			[
				'links' => AsUrlResources::class,
				'questions' => AsDemonstrationQuestions::class,
				'allow_rating' => 'boolean',
				'online_submission' => 'boolean',
				'open_submission' => 'boolean',
				'submit_after_due' => 'boolean',
				'share_submissions' => 'boolean',
				'shareable' => 'boolean',
			];
	}
	
	public function workFiles(): MorphToMany|BelongsToMany
	{
		return $this->morphToMany(WorkFile::class, 'fileable');
	}
	
	public function getWorkStorageKey(): WorkStoragesInstances
	{
		return WorkStoragesInstances::LearningDemonstrationWork;
	}
	
	public function shouldBePublic(): bool
	{
		return false;
	}
	
	public function owner(): BelongsTo
	{
		return $this->belongsTo(Person::class, 'person_id');
	}
	
	public function course(): BelongsTo
	{
		return $this->belongsTo(Course::class, 'course_id');
	}
	
	public function createdBy(): BelongsTo
	{
		return $this->belongsTo(LearningDemonstrationTemplate::class, 'created_by');
	}
	
	public function canShare(): bool
	{
		return $this->created_by == null;
	}
	
	public function type(): BelongsTo
	{
		return $this->belongsTo(LearningDemonstrationType::class, 'type_id');
	}
	
	public function skills(): BelongsToMany
	{
		return $this->belongsToMany(Skill::class, 'learning_demonstration_template_skill',
			'template_id', 'skill_id')
			->withPivot(['rubric', 'weight', 'id'])
			->as('assessment')
			->using(LearningDemonstrationTemplateSkill::class);
	}
	
	public function assessments(): HasMany
	{
		return $this->hasMany(LearningDemonstrationTemplateSkill::class, 'template_id');
	}
}
