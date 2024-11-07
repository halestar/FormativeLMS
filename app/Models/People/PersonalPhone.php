<?php

namespace App\Models\People;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class PersonalPhone extends MorphPivot
{

    public $timestamps = false;
    protected $table = "phoneables";
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

    public function phone(): BelongsTo
    {
        return $this->belongsTo(Phone::class, 'phone_id');
    }

    public function __toString(): string
    {
        return $this->phone->prettyPhone;
    }
}
