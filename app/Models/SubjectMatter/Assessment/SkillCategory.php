<?php

namespace App\Models\SubjectMatter\Assessment;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkillCategory extends Model
{
	public $timestamps = false;
	public $incrementing = true;
	protected $table = "skill_categories";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'name',
		];
	
	public function isRoot(): bool
	{
		return !$this->parent_id;
	}
	
	public function parentCategory(): BelongsTo
	{
		return $this->belongsTo(self::class, 'parent_id');
	}
	
	public function canDelete(): bool
	{
		return ($this->subCategories()->count() == 0) && ($this->skills()->count() == 0);
	}
	
	public function subCategories(): HasMany
	{
		return $this->hasMany(self::class, 'parent_id');
	}
	
	public function skills(): BelongsToMany
	{
		return $this->belongsToMany(Skill::class, 'skill_category_designation', 'category_id',
			'skill_id')
		            ->withPivot('designation')
		            ->as('designation');
	}
	
	#[Scope]
	protected function root(Builder $query): void
	{
		$query->whereNull('parent_id');
	}
}
