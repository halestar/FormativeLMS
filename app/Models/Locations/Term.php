<?php

namespace App\Models\Locations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    public $timestamps = true;
    protected $table = "terms";
    protected $primaryKey = "id";
    public $incrementing = true;
    protected $fillable =
        [
            'campus_id',
            'year_id',
            'label',
            'term_start',
            'term_end',
        ];

    protected function casts(): array
    {
        return
            [
                'term_start' => 'date: ' . config('lms.date_format'),
                'term_end' => 'date: ' . config('lms.date_format'),
            ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('term-start-order', function (Builder $builder)
        {
            $builder->orderBy('term_start');
        });
    }

    public function canDelete(): bool
    {
        return true;
    }
}
