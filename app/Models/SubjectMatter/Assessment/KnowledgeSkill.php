<?php

namespace App\Models\SubjectMatter\Assessment;

use App\Casts\Rubric;
use App\Interfaces\HasRubric;
use App\Models\CRUD\Level;
use App\Models\SubjectMatter\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laravel\Scout\Searchable;

class KnowledgeSkill extends Model implements HasRubric
{
    use Searchable;
    protected $with = ['subject'];
    public $timestamps = true;
    protected $table = "knowledge_skills";
    protected $primaryKey = "id";
    public $incrementing = true;
    protected $fillable =
        [
            'subject_id',
            'designation',
            'name',
            'description',
        ];

    protected function casts(): array
    {
        return
            [
                'rubric' => Rubric::class,
                'active' => 'boolean',
            ];
    }

    public function toSearchableArray(): array
    {
        return
            [
                'designation' => $this->designation,
                'name' => $this->name,
                'description' => $this->description,
            ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function levels(): BelongsToMany
    {
        return $this->belongsToMany(Level::class, 'knowledge_skill_levels',
            'skill_id', 'level_id');
    }

    public function categories(): MorphToMany
    {
        return $this->morphToMany(SkillCategory::class, 'skill', 'skill_category_designation', 'skill_id', 'category_id')
            ->withPivot(['designation_id'])
            ->as('info')
            ->using(SkillCategoryDesignation::class);
    }

    public function canActivate(): bool
    {
        return ($this->rubric != null);
    }

    public function getRubric()
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
}
