<?php

namespace App\Models\Substitutes;

use App\Models\Locations\Campus;
use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class SubstituteCampusRequest extends Model
{
    protected $table = 'substitute_campus_requests';

    protected $primaryKey = 'id';

    public $timestamps = true;

    public $incrementing = true;

    protected $with = ['campus'];

    public $guarded = ['id'];

    protected function casts(): array
    {
        return
            [
                'responded_on' => 'datetime',
            ];
    }

    public function subRequest(): BelongsTo
    {
        return $this->belongsTo(SubstituteRequest::class, 'request_id');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function substitute(): BelongsTo
    {
        return $this->belongsTo(Substitute::class, 'substitute_id');
    }

    public function classRequests(): HasMany
    {
        return $this->hasMany(SubstituteClassRequest::class, 'campus_request_id');
    }

    public function availableSubs(): Collection
    {
        return Person::role(SchoolRoles::$SUBSTITUTE)->with('substituteProfile')->get();
    }
}
