<?php

namespace App\Models\People;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use JsonSerializable;

class PersonalAddress extends MorphPivot implements Arrayable, JsonSerializable
{
	public $timestamps = false;
	protected $table = 'addressable';
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

	public function prettyAddress(): string
	{
		return $this->address->pretty_address . ($this->label ? '(' . $this->label . ')' : '') .
		       ($this->primary ? ' [Primary]' : '');
	}

	public function __toString(): string
	{
		return $this->address->pretty_address;
	}

	protected function casts(): array
	{
		return
			[
				'primary' => 'boolean',
			];
	}
}
