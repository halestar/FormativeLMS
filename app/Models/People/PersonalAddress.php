<?php

namespace App\Models\People;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class PersonalAddress extends MorphPivot
{

    public $timestamps = false;
    protected $table = "addressable";
    protected function casts(): array
    {
        return
            [
                'primary' => 'boolean',
            ];
    }

    protected $fillable =
        [
            'primary',
            'label',
            'order',
        ];

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }


    public function __toString(): string
    {
        return $this->address->prettyAddress;
    }
}
