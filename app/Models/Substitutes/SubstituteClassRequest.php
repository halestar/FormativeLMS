<?php

namespace App\Models\Substitutes;

use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubstituteClassRequest extends Model
{
    protected $table = 'substitute_class_requests';

    protected $primaryKey = 'id';

    public $timestamps = true;

    public $incrementing = true;

    public $guarded = ['id'];

    protected function casts(): array
    {
        return
            [
                'start_on' => 'datetime:Y-m-d h:i A',
                'end_on' => 'datetime:Y-m-d h:i A',
            ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'session_id');
    }

    public function campusRequest(): BelongsTo
    {
        return $this->belongsTo(SubstituteCampusRequest::class, 'campus_request_id');
    }

    public function substitute(): BelongsTo
    {
        return $this->belongsTo(Substitute::class, 'person_id', 'person_id');
    }

    public function hasSub()
    {
        return $this->person_id != null;
    }
}
