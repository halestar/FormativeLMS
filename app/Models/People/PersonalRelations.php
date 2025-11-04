<?php

namespace App\Models\People;

use App\Models\SystemTables\Relationship;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PersonalRelations extends Pivot
{
	
	public $timestamps = false;
	protected $table = "people_relations";
	protected $with = ['relationship'];
	
	protected $fillable =
		[
			'relationship_id',
		];
	
	public function from(): BelongsTo
	{
		return $this->belongsTo(Person::class, 'from_person_id');
	}
	
	public function to(): BelongsTo
	{
		return $this->belongsTo(Person::class, 'to_person_id');
	}
	
	public function relationship(): BelongsTo
	{
		return $this->belongsTo(Relationship::class, 'relationship_id');
	}
}
