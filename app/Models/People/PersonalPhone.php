<?php

namespace App\Models\People;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class PersonalPhone extends MorphPivot
{
    public $timestamps = false;

    protected $table = 'phoneables';

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

    public function prettyPhone(): string
    {
        return $this->phone->pretty_phone.($this->label ? '('.$this->label.')' : '').
               ($this->primary ? ' [Primary]' : '');
    }

    public function __toString(): string
    {
        return $this->prettyPhone();
    }

    protected function casts(): array
    {
        return
            [
                'primary' => 'boolean',
            ];
    }
}
