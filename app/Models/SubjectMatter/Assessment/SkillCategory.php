<?php

namespace App\Models\SubjectMatter\Assessment;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

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
		//we can only delete if we don't have any subcategories
		$canDelete = true;
		if($this->subCategories()
		        ->count() > 0)
			return false;
		//and no skills
		if($this->knowledgeSkills()
		        ->count() > 0 || $this->characterSkills()
		                              ->count() > 0)
			return false;
		return $canDelete;
	}
	
	public function subCategories(): HasMany
	{
		return $this->hasMany(self::class, 'parent_id');
	}
	
	public function knowledgeSkills(): MorphToMany
	{
		return $this->morphedByMany(KnowledgeSkill::class, 'skill', 'skill_category_designation', 'category_id',
			'skill_id')
		            ->withPivot(['designation_id'])
		            ->as('info')
		            ->using(SkillCategoryDesignation::class);
	}
	
	public function characterSkills(): MorphToMany
	{
		return $this->morphedByMany(CharacterSkill::class, 'skill', 'skill_category_designation', 'category_id',
			'skill_id')
		            ->withPivot(['designation_id'])
		            ->as('info')
		            ->using(SkillCategoryDesignation::class);
	}
	
	#[Scope]
	protected function root(Builder $query): void
	{
		$query->whereNull('parent_id');
	}
}
