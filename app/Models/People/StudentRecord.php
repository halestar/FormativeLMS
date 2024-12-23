<?php

namespace App\Models\People;

use App\Models\CRUD\DismissalReason;
use App\Models\CRUD\Level;
use App\Models\Locations\Campus;
use App\Models\Locations\Year;
use App\Models\SubjectMatter\ClassSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StudentRecord extends Model
{
    protected $with = ['campus', 'year', 'person', 'level'];
    public $timestamps = true;
    protected $table = "student_records";
    protected $primaryKey = "id";
    public $incrementing = true;
    protected $fillable =
        [
            'campus_id',
            'year_id',
            'level_id',
            'start_date',
            'end_date',
            'dismissal_reason_id',
            'dismissal_note',
        ];

    protected function casts(): array
    {
        return
            [
                'start_date' => 'date: m/d/y',
                'end_date' => 'date: m/d/y',
                'created_at' => 'datetime: m/d/Y h:i A',
                'updated_at' => 'datetime: m/d/Y h:i A',
            ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class, 'year_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function dismissalReason(): BelongsTo
    {
        return $this->belongsTo(DismissalReason::class, 'dismissal_reason_id');
    }

    public function canRemove(): bool
    {
        return true;
    }

    public function classSessions(): BelongsToMany
    {
        return $this->belongsToMany(ClassSession::class, 'class_sessions_students', 'student_id', 'session_id');
    }
}
