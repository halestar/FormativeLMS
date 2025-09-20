<?php

/**
 * @var \App\Classes\ClassManagement\ClassSessionLayoutManager $layout
 */

namespace App\Models\SubjectMatter;

use App\Classes\ClassManagement\ClassSessionLayoutManager;
use App\Interfaces\HasSchedule;
use App\Models\Locations\Room;
use App\Models\Locations\Term;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\Schedules\Block;
use App\Models\Schedules\Period;
use App\Models\SubjectMatter\Components\ClassMessage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ClassSession extends Model implements HasSchedule
{
	public $timestamps = true;
	public $incrementing = true;
	/**
	 * @var \App\Classes\ClassManagement\ClassSessionLayoutManager $layout
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
	
	public function layout(): Attribute
	{
		return Attribute::make(
			get: fn($value, $attributes) => new ClassSessionLayoutManager($value ?? "[]", $this),
			set: fn($value, $attributes) => json_encode($value),
		);
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
		return false;
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
			
			];
	}
}
