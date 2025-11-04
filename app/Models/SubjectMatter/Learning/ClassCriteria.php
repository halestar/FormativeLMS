<?php

namespace App\Models\SubjectMatter\Learning;

use App\Models\Locations\Campus;
use App\Models\Locations\Year;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\SchoolClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ClassCriteria extends Model
{
	public $timestamps = false;
	public $incrementing = true;
	protected $table = "class_criteria";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'class_id',
			'name',
			'abbreviation',
		];

	
	public function schoolClass(): BelongsTo
	{
		return $this->belongsTo(SchoolClass::class, 'class_id');
	}
	
	public function sessions(): BelongsToMany
	{
		return $this->belongsToMany(ClassSession::class, 'class_session_criteria', 'criteria_id', 'session_id')
			->withPivot('weight')
			->as('sessionCriteria')
			->using(ClassSessionCriteria::class);
	}
	
	public static function sourceYears(SelectorParams $params): Collection
	{
		$user = auth()->user();
		return Year::select(['years.*', DB::raw('COUNT(class_criteria.id) as criteria_count')])
			->join('school_classes', 'school_classes.year_id', '=', 'years.id')
			->join('class_sessions', 'class_sessions.class_id', '=', 'school_classes.id')
			->join('class_sessions_teachers', 'class_sessions_teachers.session_id', '=', 'class_sessions.id')
			->join('class_criteria', 'class_criteria.class_id', '=', 'school_classes.id')
			->where('class_sessions_teachers.person_id', $user->id)
			->having('criteria_count', '>', 0)
			->groupBy('years.id')
			->orderBy('years.year_start', 'desc')
			->get();
	}
	
	public static function sourceCampus(SelectorParams $params): Collection
	{
		$user = auth()->user();
		return Campus::select(['campuses.*', DB::raw('COUNT(class_criteria.id) as criteria_count')])
		           ->join('terms', 'terms.campus_id', '=', 'campuses.id')
		           ->join('class_sessions', 'class_sessions.term_id', '=', 'terms.id')
		           ->join('class_sessions_teachers', 'class_sessions_teachers.session_id', '=', 'class_sessions.id')
		           ->join('class_criteria', 'class_criteria.class_id', '=', 'school_classes.id')
		           ->where('class_sessions_teachers.person_id', $user->id)
		           ->having('criteria_count', '>', 0)
		           ->groupBy('campus.id')
		           ->orderBy('campuses.name')
		           ->get();
	}
}
