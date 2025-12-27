<?php

namespace App\Models\SubjectMatter\Learning;

use App\Casts\Learning\AsDemonstrationQuestions;
use App\Casts\Learning\AsUrlResources;
use App\Enums\WorkStoragesInstances;
use App\Interfaces\Fileable;
use App\Models\Locations\Year;
use App\Models\People\Person;
use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\SubjectMatter\ClassSession;
use App\Models\Utilities\WorkFile;
use App\Traits\HasLogs;
use App\Traits\HasWorkFiles;
use Database\Factories\Learning\DemonstrationFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[UseFactory(DemonstrationFactory::class)]
class LearningDemonstration extends Model implements Fileable
{
	use HasUuids, HasWorkFiles, HasLogs, HasFactory;
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
			'auto_turn_in',
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
				'auto_turn_in' => 'date: m/d/Y',
			];
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
	
	public function skills(): BelongsToMany
	{
		return $this->belongsToMany(Skill::class, 'learning_demonstration_skills', 'demonstration_id', 'skill_id')
		            ->withPivot(['rubric', 'weight', 'id'])
					->as('assessment')
		            ->using(LearningDemonstrationSkill::class);
	}
	
	public function classSessions(): BelongsToMany
	{
		return $this->belongsToMany(ClassSession::class, 'learning_demonstration_class_sessions', 'demonstration_id', 'session_id')
		            ->withPivot(['id', 'criteria_id', 'criteria_weight', 'posted_on', 'due_on'])
		            ->using(LearningDemonstrationClassSession::class)
		            ->as('session');
	}

	public function demonstrationSessions(): HasMany
	{
		return $this->hasMany(LearningDemonstrationClassSession::class, 'demonstration_id');
	}

	public function demonstrationSession(ClassSession $session): LearningDemonstrationClassSession
	{
		return $this->demonstrationSessions()->where('session_id', $session->id)->first();
	}

	public function opportunities(): HasManyThrough
	{
		return $this->hasManyThrough(LearningDemonstrationOpportunity::class, LearningDemonstrationClassSession::class, 'demonstration_id', 'demonstration_session_id');
	}

	public function canDelete(): bool
	{
		return true;
	}

	public function canAccessFile(Person $person, WorkFile $file): bool
	{
		return true;
	}
}
