<?php

namespace App\Models\People;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PersonalPhone extends Pivot
{

    public $timestamps = false;
    protected $table = "people_phones";
    protected function casts(): array
    {
        return
            [
                'primary' => 'boolean',
                'work' => 'boolean',
            ];
    }

    protected $fillable =
        [
            'primary',
            'work',
        ];

    public function phone(): BelongsTo
    {
        return $this->belongsTo(Phone::class, 'phone_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function __toString(): string
    {
        return $this->phone->prettyPhone;
    }
}
