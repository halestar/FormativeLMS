<?php

namespace App\Models\Substitutes;

use App\Models\Locations\Year;
use App\Models\People\Person;
use App\Models\People\Phone;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

class Substitute extends Model
{
    protected $table = 'substitutes';

    protected $primaryKey = 'person_id';

    public $timestamps = true;

    public $incrementing = false;

    protected $with = ['person'];

    public $guarded = [];

    protected function casts(): array
    {
        return
            [
                'sms_confirmed' => 'boolean',
                'email_confirmed' => 'boolean',
                'account_verified' => 'datetime',
                'sms_verified' => 'datetime',
                'active' => 'boolean',
            ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function phone(): BelongsTo
    {
        return $this->belongsTo(Phone::class, 'phone_id');
    }

    public function createAccessToken(): SubstituteToken
    {
        return SubstituteToken::create(
            [
                'substitute_id' => $this->id,
                'campus_request_id' => null,
                'expires_at' => now()->addDays(1),
            ]);
    }

    public function createRequestToken(SubstituteRequest $subRequest): SubstituteToken
    {
        return SubstituteToken::create(
            [
                'substitute_id' => $this->id,
                'request_id' => $subRequest->id,
                'expires_at' => now()->addDays(1),
            ]);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('active', true);
    }

    public function hasConflicts(SubstituteRequest $request): bool
    {
        // first, are there any other sub requests on this day that this person has access to?
        $classesSubbed = SubstituteClassRequest::select('substitute_class_requests.*')
            ->join('substitute_campus_requests', 'substitute_campus_requests.id', '=', 'substitute_class_requests.campus_request_id')
            ->join('substitute_requests', 'substitute_requests.id', '=', 'substitute_campus_requests.request_id')
            ->where('substitute_campus_requests.substitute_id', '=', $this->id)
            ->whereDate('substitute_requests.requested_for', '=', $request->requested_for)
            ->get();
        if ($classesSubbed->count() > 0) {
            return false;
        }
        foreach ($request->classRequests as $class) {
            foreach ($classesSubbed as $subbed) {
                if ($subbed->start_on == $class->start_on || $subbed->end_on == $class->end_on) {
                    return true;
                }
                if ($subbed->start_on >= $class->start_on && $subbed->start_on <= $class->end_on) {
                    return true;
                }
                if ($subbed->end_on <= $class->end_on && $subbed->end_on >= $class->start_on) {
                    return true;
                }
            }
        }

        return false;
    }

    public function subbedSubstituteCampusRequests(): HasMany
    {
        return $this->hasMany(SubstituteCampusRequest::class, 'substitute_id');
    }

    public function subbedClassRequests(): MorphMany
    {
        return $this->morphMany(SubstituteClassRequest::class, 'substitutable');
    }

    public function subbedClassSession(SubstituteClassRequest $session): Collection
    {
        return $this->subbedClassRequests()->where('sub_class_requests.session_id', $session->id)
            ->union(
                ClassRequest::select('sub_class_requests.*')
                    ->join('sub_campus_requests', 'sub_campus_requests.id', '=', 'sub_class_requests.campus_request_id')
                    ->where('sub_class_requests.session_id', $session->id)
                    ->where('sub_campus_requests.substitute_id', $this->id)
            )
            ->groupBy('sub_class_requests.id')
            ->get();
    }

    public function totalSubbedInYear(?Year $year = null): int
    {
        if (! $year) {
            $year = Year::currentYear();
        }

        return $this->subbedSubstituteCampusRequests()
            ->whereBetween('created_at', [$year->start, $year->end])
            ->count() +
               $this->subbedClassRequests()
                   ->whereBetween('created_at', [$year->start, $year->end])
                   ->count();
    }
}
