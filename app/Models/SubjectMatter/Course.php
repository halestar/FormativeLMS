<?php

namespace App\Models\SubjectMatter;

use App\Models\Locations\Campus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Course extends Pivot
{
    protected $with = ['subject'];
    public $timestamps = true;
    protected $table = "courses";
    protected $primaryKey = "id";
    public $incrementing = true;
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

    public function courseName(): Attribute
    {
        return Attribute::make
        (
            get: fn (?string $value, $attributes) => $attributes['name'] . ($attributes['subtitle'] ? ": $attributes[subtitle]" : "") .
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

}
