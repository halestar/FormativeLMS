<?php

namespace App\Traits;

use App\Models\People\Address;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait SingleAddressable
{
	public function address(): BelongsTo
	{
		return $this->belongsTo(Address::class, 'address_id');
	}
	
	public function isSingleAddressable(): bool
	{
		return true;
	}
}
