<?php

namespace App\Models\SubjectMatter;

use App\Models\Locations\Room;
use App\Models\Schedules\Period;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClassSessionPeriod extends Pivot
{
    public $timestamps = false;
    protected $table = "class_sessions_periods";
    protected $with = ['room'];

    protected $fillable =
        [
            'room_id',
        ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'session_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class, 'period_id');
    }
}
