<?php

/**
 * @var \App\Classes\Integrators\Local\ClassManagement\ClassSessionLayoutManager $layout
 */

namespace App\Models\SubjectMatter;

use App\Classes\Integrators\Local\ClassManagement\ClassSessionLayoutManager;
use App\Classes\Settings\SchoolSettings;
use App\Enums\AssessmentStrategyCalculationMethod;
use App\Enums\ClassViewer;
use App\Interfaces\HasSchedule;
use App\Models\Integrations\IntegrationConnection;
use App\Models\Locations\Room;
use App\Models\Locations\Term;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\Schedules\Block;
use App\Models\Schedules\Period;
use App\Models\SubjectMatter\Components\ClassCommunicationObject;
use App\Models\SubjectMatter\Components\ClassMessage;
use App\Models\SubjectMatter\Learning\ClassCriteria;
use App\Models\SubjectMatter\Learning\ClassSessionCriteria;
use App\Models\SubjectMatter\Learning\LearningDemonstration;
use App\Models\SubjectMatter\Learning\LearningDemonstrationClassSession;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClassSession extends Model implements HasSchedule
{
	public $timestamps = true;
	public $incrementing = true;
	/**
	 * @var \App\Classes\Integrators\Local\ClassManagement\ClassSessionLayoutManager $layout
	 */
	protected $with = ['schoolClass', 'term'];
	protected $table = "class_sessions";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'class_id',
			'term_id',
			'room_id',
			'block_id',
			'inherit_criteria',
			'class_management_id',
		];
	
	public function canDelete(): bool
	{
		return true;
	}
	
	public function schoolClass(): BelongsTo
	{
		return $this->belongsTo(SchoolClass::class, 'class_id');
	}
	
	public function course(): HasOneThrough
	{
		return $this->hasOneThrough(Course::class, SchoolClass::class, 'id', 'id', 'class_id', 'course_id');
	}
	
	public function term(): BelongsTo
	{
		return $this->belongsTo(Term::class, 'term_id');
	}
	
	public function room(): BelongsTo
	{
		return $this->belongsTo(Room::class, 'room_id');
	}
	
	public function block(): BelongsTo
	{
		return $this->belongsTo(Block::class, 'block_id');
	}
	
	public function isClassStudent(StudentRecord $student): bool
	{
		return $this->students()
		            ->where('student_records.id', $student->id)
		            ->exists();
	}
	
	public function students(): BelongsToMany
	{
		return $this->belongsToMany(StudentRecord::class, 'class_sessions_students', 'session_id', 'student_id');
	}
	
	public function periods(): BelongsToMany
	{
		return $this->belongsToMany(Period::class, 'class_sessions_periods', 'session_id', 'period_id')
		            ->withPivot('room_id')
		            ->using(ClassSessionPeriod::class)
		            ->as('sessionPeriod');
	}
	
	public function name(): Attribute
	{
		return Attribute::make(
			get: fn() => $this->course->name
		);
	}
	
	public function fullName(): Attribute
	{
		return Attribute::make(
			get: fn() => $this->name . ' (' . $this->teachersString() . ') [' . $this->scheduleString() . ']'
		);
	}
	
	public function nameWithSchedule(): Attribute
	{
		return Attribute::make(
			get: fn() => $this->name . ' [' . $this->scheduleString() . ']'
		);
	}
	
	public function teachersString(): string
	{
		return $this->teachers->pluck('name')
		                      ->join(', ');
	}
	
	public function scheduleString(): string
	{
		$periods = $this->classPeriods()
		                ->pluck('abbr')
		                ->join(', ');
		if($this->block_id)
			return $this->block->name . " (" . $periods . ")";
		return $periods;
	}
	
	public function classPeriods(): Collection
	{
		if(!$this->block_id)
			return $this->periods;
		return $this->block->periods;
	}
	
	public function locationString(bool $withLinks = false, string $attr = null): string
	{
		if($this->room_id)
		{
			if($withLinks)
				return '<a href="' . route('locations.rooms.show', ['room' => $this->room_id]) . '" ' . $attr . ' >' .
					$this->room->name . '</a>';
			return $this->room->name;
		}
		if($this->block_id)
			return __('common.tbd');
		$rooms = [];
		foreach($this->periods as $period)
		{
			if($withLinks)
				$room_str = '<a href="' . route('locations.rooms.show', ['room' => $period->sessionPeriod->room_id]) .
					'" ' . $attr . ' >' . $period->sessionPeriod->room->name . '</a>';
			else
				$room_str = $period->sessionPeriod->room->name;
			$rooms[$room_str][] = $period->abbr;
		}
		$str = [];
		foreach($rooms as $room => $periods)
		{
			$str[] = $room . " (" . implode(', ', $periods) . ")";
		}
		return implode(', ', $str);
	}
	
	public function isEnrolled(StudentRecord $student): bool
	{
		return $this->students()
		            ->where('student_records.id', $student->id)
		            ->exists();
	}
	
	public function getSchedule(): Collection
	{
		return $this->classPeriods();
	}
	
	public function getScheduleLabel(): string
	{
		return $this->name . ' ' . $this->scheduleString();
	}
	
	public function getScheduleColor(): string
	{
		return $this->course->subject->color;
	}
	
	public function getScheduleTextColor(): string
	{
		return $this->course->subject->getTextHex();
	}
	
	public function getScheduleLink(): ?string
	{
		return null;
	}
	
	public function hasUnseenMessages(StudentRecord $student): bool
	{
		//what kind of user is this?
		$viewer = Auth::user();
		$type = null;
		if($viewer->isTeacher() && $this->isClassTeacher($viewer))
			$type = 'teacher_read';
		elseif($viewer->isStudent() && $viewer->student->id == $student->id)
			$type = 'student_read';
		elseif($viewer->isParent() && $viewer->isParentOfPerson($student->person))
			$type = 'parent_read';
		return $this->messages()
		            ->where('student_id', $student->id)
		            ->where($type, false)
		            ->count() > 0;
		
	}
	
	public function isClassTeacher(Person $person): bool
	{
		return $this->teachers()
		            ->where('person_id', $person->id)
		            ->exists();
	}
	
	public function teachers(): BelongsToMany
	{
		return $this->belongsToMany(Person::class, 'class_sessions_teachers', 'session_id', 'person_id');
	}
	
	public function messages(): HasMany
	{
		return $this->hasMany(ClassMessage::class, 'session_id');
	}
	
	protected function casts(): array
	{
		return
			[
				'setup_done' => 'boolean',
				'inherit_criteria' => 'boolean',
                'layout' => 'array',
				'calc_method' => AssessmentStrategyCalculationMethod::class,
			];
	}
	
	public function classCriteria(): BelongsToMany
	{
		return $this->belongsToMany(ClassCriteria::class, 'class_session_criteria', 'session_id', 'criteria_id')
			->withPivot('weight')
			->as('sessionCriteria')
			->using(ClassSessionCriteria::class);
	}
	
	public function hasCriteria(ClassCriteria $criteria): bool
	{
		return $this->classCriteria()
			->where('class_criteria.id', $criteria->id)
			->exists();
	}
	
	public function getCriteria(ClassCriteria $criteria): ?ClassCriteria
	{
		return $this->classCriteria()
			->where('class_criteria.id', $criteria->id)
			->first();
	}

    public function classManager(): BelongsTo
    {
        if(!$this->class_management_id)
        {
            //There isn't one set, so we use the system's default one.
            $settings = app(SchoolSettings::class);
            $this->class_management_id = $settings->class_management_connection_id;
            $this->save();
        }
        return $this->belongsTo(IntegrationConnection::class, 'class_management_id');
    }

    public function viewingAs(ClassViewer $viewer): bool
    {
        $user = Auth()->user();
        return $user->classViewRole($this) == $viewer;
    }

    public function hasConversationAccess(StudentRecord $student): bool
    {
        $user = Auth()->user();
        $viewRole = $user->classViewRole($this);
        if(!$viewRole)
            return false;

        if($viewRole == ClassViewer::FACULTY)
            return $this->isClassTeacher($user);

        if($viewRole == ClassViewer::STUDENT && $student->person_id == $user->id)
            return true;

        if($viewRole == ClassViewer::PARENT && $user->isParentOfPerson($student->person))
            return true;

        if($viewRole == ClassViewer::ADMIN && $user->studentTrackee()->where('student_records.id', $student->id)->exists())
            return true;

        return false;
    }

	public function communicationObjects(string $type): HasMany
	{
		return $this->hasMany(ClassCommunicationObject::class, 'session_id')
			->where('className', $type);
	}

	public function demonstrations(): BelongsToMany
	{
		return $this->belongsToMany(LearningDemonstration::class, 'learning_demonstration_class_sessions', 'session_id', 'demonstration_id')
			->withPivot(['id', 'criteria_id', 'criteria_weight', 'posted_on', 'due_on'])
			->using(LearningDemonstrationClassSession::class)
			->as('session')
			->orderBy('learning_demonstration_class_sessions.posted_on', 'desc');
	}
}
