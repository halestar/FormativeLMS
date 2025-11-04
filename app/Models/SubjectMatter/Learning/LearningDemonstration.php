<?php

namespace App\Models\SubjectMatter\Learning;

use App\Enums\WorkStoragesInstances;
use App\Interfaces\Fileable;
use App\Models\Locations\Year;
use App\Models\People\Person;
use App\Models\SubjectMatter\Assessment\CharacterSkill;
use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Course;
use App\Traits\HasUuidWorkFiles;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class LearningDemonstration extends Model implements Fileable
{
	use HasUuids, HasUuidWorkFiles;
	protected $with = ['type', 'knowledgeSkills', 'characterSkills', 'criteria'];
	public $timestamps = true;
	public $incrementing = false;
	protected $table = "learning_demonstrations";
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
		];
	
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
	
	public function type(): BelongsTo
	{
		return $this->belongsTo(LearningDemonstrationType::class, 'type_id');
	}
	
	public function year(): BelongsTo
	{
		return $this->belongsTo(Year::class, 'year_id');
	}
	
	public function template(): BelongsTo
	{
		return $this->belongsTo(LearningDemonstrationTemplate::class, 'template_id');
	}
	
	public function criteria(): BelongsTo
	{
		return $this->belongsTo(ClassCriteria::class, 'criteria_id');
	}
	
	public function knowledgeSkills(): MorphToMany
	{
		return $this->morphToMany(Skill::class, 'skillable', 'learning_demonstration_template_skill', 'template_id')
		            ->withPivot(['rubric', 'id'])
		            ->as('knowledge')
		            ->using(LearningDemonstrationTemplateSkill::class);
	}
	
	public function characterSkills(): MorphToMany
	{
		return $this->morphToMany(CharacterSkill::class, 'skillable', 'learning_demonstration_template_skill', 'template_id')
		            ->withPivot(['rubric', 'id'])
		            ->as('character')
		            ->using(LearningDemonstrationTemplateSkill::class);
	}
	
	public function classSessions(): BelongsToMany
	{
		return $this->belongsToMany(ClassSession::class, 'learning_demonstrations_class_sessions', 'demonstration_id', 'session_id')
		            ->withPivot(['id', 'posted_on', 'due_on'])
		            ->using(LearningDemonstrationClassSession::class)
		            ->as('session');
	}
	
}
