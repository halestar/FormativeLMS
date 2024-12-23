<?php

namespace App\Models\SubjectMatter;

use App\Models\Locations\Campus;
use App\Models\Scopes\OrderByOrderScope;
use App\Traits\DeterminesTextColor;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[ScopedBy(OrderByOrderScope::class)]
class Subject extends Model
{
    use DeterminesTextColor;
    protected $with = ['campus'];
    public $timestamps = true;
    protected $table = "subjects";
    protected $primaryKey = "id";
    public $incrementing = true;
    protected $fillable =
        [
            'campus_id',
            'name',
            'color',
            'required_terms',
            'order',
            'active',
        ];

    protected function casts(): array
    {
        return
            [
                'required_terms' => 'integer',
                'order' => 'integer',
                'active' => 'boolean',
            ];
    }

    public function canDelete(): bool
    {
        return true;
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'subject_id');
    }

    public function schoolClasses(): HasManyThrough
    {
        return $this->hasManyThrough(SchoolClass::class, Course::class, 'subject_id', 'course_id');
    }

    public function scopeActive(Builder $builder)
    {
        $builder->where('active', true);
    }

}
