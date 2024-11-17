<?php

namespace App\Traits;

use App\Models\People\Phone;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait SinglePhoneable
{

    public function phone(): BelongsTo
    {
        return $this->belongsTo(Phone::class, 'phone_id');
    }

    public function isSinglePhoneable(): bool
    {
        return true;
    }
}
