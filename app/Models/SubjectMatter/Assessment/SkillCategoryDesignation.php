<?php

namespace App\Models\SubjectMatter\Assessment;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class SkillCategoryDesignation extends MorphPivot
{
    public $timestamps = false;
    protected $table = "skill_category_designation";
    protected $with = ['designation'];

    protected $fillable =
        [
            'designation_id',
        ];

    public function designation(): BelongsTo
    {
        return $this->belongsTo(\App\Models\CRUD\SkillCategoryDesignation::class, 'designation_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SkillCategory::class, 'category_id');
    }
}
