<?php

namespace App\Models\SubjectMatter\Assessment;

use App\Casts\Rubric;
use App\Interfaces\HasRubric;
use App\Traits\Leveable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laravel\Scout\Searchable;

class CharacterSkill extends Model implements HasRubric
{
	use Searchable, Leveable;
	
	public $timestamps = true;
	public $incrementing = true;
	protected $table = "character_skills";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'designation',
			'name',
			'description',
		];
	
	public function toSearchableArray(): array
	{
		return
			[
				'designation' => $this->designation,
				'name' => $this->name,
				'description' => $this->description,
			];
	}
	
	public function categories(): MorphToMany
	{
		return $this->morphToMany(SkillCategory::class, 'skill', 'skill_category_designation', 'skill_id',
			'category_id')
		            ->withPivot(['designation_id'])
		            ->as('info')
		            ->using(SkillCategoryDesignation::class);
	}
	
	public function canActivate(): bool
	{
		return ($this->rubric != null);
	}
	
	public function getRubric(): ?Rubric
	{
		return $this->rubric;
	}
	
	public function setRubric(Rubric $rubric)
	{
		$this->rubric = $rubric;
	}
	
	public function getDescription(): string
	{
		return $this->description;
	}
	
	public function getSkillId(): int
	{
		return $this->id;
	}
	
	public function getSkillName(): string
	{
		return $this->name ?? $this->designation;
	}
	
	protected function casts(): array
	{
		return
			[
				'rubric' => Rubric::class,
				'active' => 'boolean',
			];
	}
}
