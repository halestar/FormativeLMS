<?php

namespace App\Models\Locations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Year extends Model
{
    public $timestamps = true;
    protected $table = "years";
    protected $primaryKey = "id";
    public $incrementing = true;
    protected $fillable =
        [
            'label',
            'year_start',
            'year_end',
        ];

    protected function casts(): array
    {
        return
            [
                'year_start' => 'date: ' . config('lms.date_format'),
                'year_end' => 'date: ' . config('lms.date_format'),
            ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('year-start-order', function (Builder $builder)
        {
            $builder->orderBy('year_start');
        });

        static::updated(function (Year $year)
        {
            // whenever we update a year, we must
            // reset the start and end dates of all the semesters
            Term::where('year_id', $year->id)
                ->whereDate('term_start', '<', $year->year_start->format('Y-m-d'))
                ->update(['term_start' => $year->year_start->format('Y-m-d')]);
            Term::where('year_id', $year->id)
                ->whereDate('term_end', '>', $year->year_end->format('Y-m-d'))
                ->update(['term_end' => $year->year_end->format('Y-m-d')]);
        });
    }

    public function canDelete(): bool
    {
        // You can't delete if there are terms defined.
        if($this->terms()->count() > 0)
            return false;

        return true;
    }

    public static function currentYear(): Year
    {
        return Cache::rememberForever('current-year', function()
        {
            $now = date('Y-m-d');
            return Year::whereDate('year_start', '<=', $now)
                ->whereDate('year_end', '>=', $now)
                ->first();
        });
    }

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class, 'year_id');
    }

    public function campusTerms(Campus $campus): HasMany
    {
        return $this->terms()->where('campus_id', $campus->id);
    }

    public function campuses(): BelongsToMany
    {
        return $this->belongsToMany(Campus::class, 'terms', 'year_id', 'campus_id')
            ->groupBy('campuses.id');
    }
}
