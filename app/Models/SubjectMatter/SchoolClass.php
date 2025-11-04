<?php

namespace App\Models\SubjectMatter;

use App\Models\Locations\Term;
use App\Models\Locations\Year;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\SubjectMatter\Learning\ClassCriteria;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SchoolClass extends Model
{
	public $timestamps = true;
	public $incrementing = true;
	protected $with = ['course', 'year'];
	protected $table = "school_classes";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'course_id',
			'year_id',
		];
	
	public function canDelete(): bool
	{
		return true;
	}
	
	public function course(): BelongsTo
	{
		return $this->belongsTo(Course::class, 'course_id');
	}
	
	public function subject(): HasOneThrough
	{
		return $this->hasOneThrough(Subject::class, Course::class, 'id', 'id', 'course_id', 'subject_id');
	}
	
	public function year(): BelongsTo
	{
		return $this->belongsTo(Year::class, 'year_id');
	}
	
	public function name(): Attribute
	{
		return Attribute::make
		(
			get: fn() => $this->course->name,
		);
	}
	
	public function termSession(Term $term): ?ClassSession
	{
		return $this->sessions()
		            ->where('term_id', $term->id)
		            ->first();
	}
	
	public function sessions(): HasMany
	{
		return $this->hasMany(ClassSession::class, 'class_id');
	}
	
	public function students(array $termIds = null): Collection
	{
		$query = StudentRecord::select('student_records.*')
		                      ->with(['campus', 'year', 'person', 'level'])
		                      ->join('class_sessions_students', 'class_sessions_students.student_id', '=',
			                      'student_records.id')
		                      ->join('class_sessions', 'class_sessions_students.session_id', '=', 'class_sessions.id')
		                      ->where('class_sessions.class_id', $this->id);
		if($termIds)
			$query->whereIn('class_sessions.term_id', $termIds);
		return $query->groupBy('student_records.id')
		             ->get();
	}
	
	/**
	 * This function is used to determine whether a student is enrolled this in this class for
	 * EVERY term passed. If termId is null, then this function will return true if the student
	 * is enrolled in any of the terms in this class. If termId is not null, then it will return
	 * true if the student is enrolled in EVERY term passed and false if they are enrolled in
	 * some or none.
	 * @param StudentRecord $student The student record to check
	 * @param array|null $termIds THe terms to check
	 * @return bool Whether the student is enrolled or not.
	 */
	public function isEnrolled(StudentRecord $student, array $termIds = null): bool
	{
		$query = DB::table('class_sessions_students')
		           ->select('class_sessions_students.student_id')
		           ->join('class_sessions', 'class_sessions_students.session_id', '=', 'class_sessions.id')
		           ->where('class_sessions.class_id', $this->id);
		if($termIds)
			$query->whereIn('class_sessions.term_id', $termIds);
		$count = $query->where('class_sessions_students.student_id', $student->id)
		               ->count();
		if(!$termIds)
			return ($count > 0);
		return ($count == count($termIds));
	}
	
	public function sessionsTaughtBy(Person $person): HasMany
	{
		return $this->sessions()
		     ->whereAttachedTo($person, 'teachers');
	}
	
	public function classCriteria(): HasMany
	{
		return $this->hasMany(ClassCriteria::class, 'class_id');
	}
	
	public function hasCriteria(): bool
	{
		return $this->classCriteria()->count() > 0;
	}
}
