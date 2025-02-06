<?php

namespace App\Models\Locations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

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

    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class, 'year_id');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public static function currentTerm(Campus $campus): ?Term
    {
        return Cache::rememberForever('current-term-' . $campus->id, function() use ($campus)
            {
                $now = date('Y-m-d');
                return Term::whereDate('term_start', '<=', $now)
                    ->whereDate('term_end', '>=', $now)
                    ->where('campus_id', $campus->id)
                    ->first();
            });
    }
}
