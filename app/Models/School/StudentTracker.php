<?php

namespace App\Models\School;

use App\Models\People\Person;
use App\Models\People\StudentRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentTracker extends Model
{

    public $timestamps = true;
    protected $table = "student_trackers";
    protected $primaryKey = "id";
    public $incrementing = true;
    protected $fillable =
        [
            'student_id',
            'person_id',
        ];

    protected function casts(): array
    {
        return
            [
                'active' => 'boolean',
            ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }

    public function tracker(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }


}
