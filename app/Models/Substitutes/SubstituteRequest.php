<?php

namespace App\Models\Substitutes;

use App\Models\Locations\Year;
use App\Models\People\Person;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SubstituteRequest extends Model
{
    protected $table = 'substitute_requests';

    protected $primaryKey = 'id';

    public $timestamps = true;

    public $incrementing = true;

    public $guarded = ['id'];

    protected function casts(): array
    {
        return
            [
                'requested_for' => 'datetime',
                'completed' => 'boolean',
                'internal' => 'boolean',
            ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'requester_id');
    }

    public function campusRequests(): HasMany
    {
        return $this->hasMany(SubstituteCampusRequest::class, 'request_id');
    }

    public function classRequests(): HasManyThrough
    {
        return $this->hasManyThrough(
            SubstituteClassRequest::class,
            SubstituteCampusRequest::class,
            'request_id',
            'campus_request_id',
            'id',
            'id'
        );
    }

    public function assignedSubstitutes(): HasManyThrough
    {
        return $this->hasManyThrough(Substitute::class, SubstituteCampusRequest::class, 'request_id', 'person_id', 'id', 'substitute_id');
    }

    #[Scope]
    protected function completed(Builder $query): void
    {
        $query->where('completed', true);
    }

    #[Scope]
    protected function incomplete(Builder $query): void
    {
        $query->where('completed', false);
    }

    #[Scope]
    protected function current(Builder $query): void
    {
        $query->whereNowOrFuture('requested_for');
    }

    #[Scope]
    protected function past(Builder $query): void
    {
        $query->wherePast('requested_for');
    }

    public function startTime(): Carbon
    {
        return new Carbon(
            DB::table('substitute_class_requests')
                ->join('substitute_campus_requests', 'substitute_campus_requests.id', '=', 'substitute_class_requests.campus_request_id')
                ->where('substitute_campus_requests.request_id', '=', $this->id)
                ->orderBy('substitute_class_requests.start_on')
                ->limit(1)
                ->value('substitute_class_requests.start_on')
        );
    }

    public function endTime(): Carbon
    {
        return new Carbon(
            DB::table('substitute_class_requests')
                ->join('substitute_campus_requests', 'substitute_campus_requests.id', '=', 'substitute_class_requests.campus_request_id')
                ->where('substitute_campus_requests.request_id', '=', $this->id)
                ->orderBy('substitute_class_requests.end_on', 'DESC')
                ->limit(1)
                ->value('substitute_class_requests.end_on')
        );
    }

    public function subStartTime(Substitute $sub): Carbon
    {
        return new Carbon(
            DB::table('substitute_class_requests')
                ->join('substitute_campus_requests', 'substitute_campus_requests.id', '=', 'substitute_class_requests.campus_request_id')
                ->where('substitute_campus_requests.request_id', '=', $this->id)
                ->where('substitute_campus_requests.substitute_id', '=', $sub->person_id)
                ->orderBy('substitute_class_requests.start_on')
                ->limit(1)
                ->value('substitute_class_requests.start_on')
        );
    }

    public function subEndTime(Substitute $sub): Carbon
    {
        return new Carbon(
            DB::table('substitute_class_requests')
                ->join('substitute_campus_requests', 'substitute_campus_requests.id', '=', 'substitute_class_requests.campus_request_id')
                ->where('substitute_campus_requests.request_id', '=', $this->id)
                ->where('substitute_campus_requests.substitute_id', '=', $sub->person_id)
                ->orderBy('substitute_class_requests.end_on', 'DESC')
                ->limit(1)
                ->value('substitute_class_requests.end_on')
        );
    }

    public function isCompleted()
    {
        return ! $this->campusRequests()->whereNull('substitute_id')->limit(1)->exists();
    }

    public function subTokens(): HasMany
    {
        return $this->hasMany(SubstituteToken::class, 'request_id');
    }

    public function subsInvited(): HasManyThrough
    {
        return $this->hasManyThrough(Substitute::class, SubstituteToken::class, 'request_id', 'person_id', 'id', 'substitute_id');
    }

    public function coveredClasses(Substitute $sub)
    {
        return SubstituteClassRequest::join('substitute_campus_requests', 'substitute_campus_requests.id', '=', 'substitute_class_requests.campus_request_id')
            ->where('substitute_campus_requests.request_id', '=', $this->id)
            ->where('substitute_campus_requests.substitute_id', '=', $sub->person_id)
            ->orderBy('substitute_class_requests.start_on')
            ->get();
    }

    public function rejectedSubs(): Collection
    {
        $subIdAvailable = DB::table('substitute_tokens')
            ->join('substitute_tokens_campuses', 'substitute_tokens_campuses.token', '=', 'substitute_tokens.token')
            ->join('substitute_campus_requests', 'substitute_campus_requests.id', '=', 'substitute_tokens_campuses.campus_request_id')
            ->select('substitute_tokens.substitute_id')
            ->where('substitute_tokens.request_id', '=', $this->id)
            ->whereNull('substitute_campus_requests.substitute_id')
            ->get()->pluck('substitute_id');

        return Person::select('people.*')
            ->join('substitute_tokens', 'substitute_tokens.substitute_id', '=', 'people.id')
            ->where('substitute_tokens.request_id', '=', $this->id)
            ->whereNotIn('people.id', $subIdAvailable)
            ->groupBy('people.id')
            ->get();
    }

    public function isResolvingInternally(): bool
    {
        return $this->completed && $this->assignedSubstitutes()->count() != $this->campusRequests()->count();
    }

    #[Scope]
    protected function thisYear(Builder $query): void
    {
        $year = Year::currentYear();
        $query->whereBetween('requested_for', [$year->start, $year->end]);
    }
}
