<?php

namespace App\Models\People;

use App\Models\CRUD\DismissalReason;
use App\Models\CRUD\Level;
use App\Models\Locations\Campus;
use App\Models\Locations\Term;
use App\Models\Locations\Year;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassMessage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class StudentRecord extends Model
{
	public $timestamps = true;
	public $incrementing = true;
	protected $with = ['campus', 'year', 'person', 'level'];
	protected $table = "student_records";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'campus_id',
			'year_id',
			'level_id',
			'start_date',
			'end_date',
			'dismissal_reason_id',
			'dismissal_note',
		];
	
	public function person(): BelongsTo
	{
		return $this->belongsTo(Person::class, 'person_id');
	}
	
	public function campus(): BelongsTo
	{
		return $this->belongsTo(Campus::class, 'campus_id');
	}
	
	public function year(): BelongsTo
	{
		return $this->belongsTo(Year::class, 'year_id');
	}
	
	public function level(): BelongsTo
	{
		return $this->belongsTo(Level::class, 'level_id');
	}
	
	public function dismissalReason(): BelongsTo
	{
		return $this->belongsTo(DismissalReason::class, 'dismissal_reason_id');
	}
	
	public function classMessages(): HasMany
	{
		return $this->hasMany(ClassMessage::class, 'student_id');
	}
	
	public function canRemove(): bool
	{
		return true;
	}
	
	public function classSessions(Term|Collection $term = null): BelongsToMany
	{
		if($term && $term instanceof Term)
			$terms = collect([$term]);
		elseif($term && $term instanceof Collection)
			$terms = $term;
		else
			$terms = Term::currentTerms();
		return $this->belongsToMany(ClassSession::class, 'class_sessions_students', 'student_id', 'session_id')
		            ->whereIn('term_id', $terms->pluck('id')
		                                       ->toArray());
	}
	
	public function latestTerm(): Term
	{
		return $this->year->terms()
		                  ->orderBy('term_start', 'desc')
		                  ->first();
	}
	
	public function tracker(): BelongsToMany
	{
		return $this->belongsToMany(Person::class, 'student_trackers', 'person_id', 'student_id');
	}
	
	public function name(): Attribute
	{
		return Attribute::make
		(
			get: fn(mixed $value, array $attributes) => $this->person->name,
		);
	}
	
	public function trackers(): BelongsToMany
	{
		return $this->belongsToMany(Person::class, 'student_trackers', 'student_id', 'person_id');
	}
	
	protected function casts(): array
	{
		return
			[
				'start_date' => 'date: m/d/y',
				'end_date' => 'date: m/d/y',
				'created_at' => 'datetime: m/d/Y h:i A',
				'updated_at' => 'datetime: m/d/Y h:i A',
			];
	}
}
