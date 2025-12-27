<?php

namespace App\Models\SubjectMatter;

use App\Models\Locations\Campus;
use App\Models\Locations\Term;
use App\Models\Locations\Year;
use App\Models\SubjectMatter\Assessment\Skill;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Collection;

class  Course extends Model
{
	public $timestamps = true;
	public $incrementing = true;
	protected $with = ['subject'];
	protected $table = "courses";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'subject_id',
			'name',
			'code',
			'subtitle',
			'description',
			'credits',
			'on_transcript',
			'gb_required',
			'honors',
			'ap',
			'can_assign_honors',
			'active',
		];
	
	public function courseName(): Attribute
	{
		return Attribute::make
		(
			get: fn(?string $value,
				$attributes) => $attributes['name'] . ($attributes['subtitle'] ? ": $attributes[subtitle]" : "") .
				($attributes['code'] ? " ($attributes[code])" : ""),
		);
	}
	
	public function canDelete(): bool
	{
		return true;
	}
	
	public function subject(): BelongsTo
	{
		return $this->belongsTo(Subject::class, 'subject_id');
	}
	
	public function campus(): HasOneThrough
	{
		return $this->hasOneThrough(Campus::class, Subject::class, 'id', 'id', 'subject_id', 'campus_id');
	}
	
	public function scopeActive(Builder $builder)
	{
		$builder->where('active', true);
	}
	
	public function schoolClasses(Year $year = null): HasMany
	{
		if(!$year)
			$year = Year::currentYear();
		return $this->hasMany(SchoolClass::class, 'course_id')
		            ->where('year_id', $year->id);
	}
	
	public function classSessions(Term $term = null): HasManyThrough
	{
		if(!$term)
			return $this->hasManyThrough(ClassSession::class, SchoolClass::class, 'course_id', 'class_id');
		return $this->hasManyThrough(ClassSession::class, SchoolClass::class, 'course_id', 'class_id')
		            ->where('term_id', $term->id);
	}
	
	protected function casts(): array
	{
		return
			[
				'credits' => 'float',
				'on_transcript' => 'boolean',
				'gb_required' => 'boolean',
				'honors' => 'boolean',
				'ap' => 'boolean',
				'can_assign_honors' => 'boolean',
				'active' => 'boolean',
			];
	}
	
	public function suggestedSkills(array $except = null): Collection
	{
		$str = trim(preg_replace('/[^a-zA-Z\s]/', '', $this->name));
		if(!$except || count($except) == 0)
			return Skill::active()->search($str)->get();
		return Skill::active()->search($str)->whereNotIn('id', $except)->get();
	}
}
