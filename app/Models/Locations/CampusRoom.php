<?php

namespace App\Models\Locations;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CampusRoom extends Pivot
{
	
	public $timestamps = false;
	protected $table = "campuses_rooms";
	protected $fillable =
		[
			'label',
			'classroom',
		];
	
	public function campus(): BelongsTo
	{
		return $this->belongsTo(Campus::class, 'campus_id');
	}
	
	public function room(): BelongsTo
	{
		return $this->belongsTo(Room::class, 'room_id');
	}
	
	protected function casts(): array
	{
		return
			[
				'classroom' => 'boolean',
			];
	}
}
