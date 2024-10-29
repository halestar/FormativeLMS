<?php

namespace App\Models\People;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PersonalAddress extends Pivot
{

    public $timestamps = false;
    protected $table = "view_policies_fields";
    protected function casts(): array
    {
        return
            [
                'primary' => 'boolean',
                'work' => 'boolean',
                'seasonal' => 'boolean',
                'season_start' => 'date',
                'season_end' => 'date',
            ];
    }

    protected $fillable =
        [
            'primary',
            'work',
            'seasonal',
            'season_start',
            'season_end',
        ];

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }




    public function __toString(): string
    {
        return $this->address->prettyAddress;
    }
}
