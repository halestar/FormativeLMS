<?php

namespace App\Models\Utilities;

use App\Enums\SystemLogType;
use App\Models\People\Person;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SystemLog extends Model
{
	use HasUuids;
	public $timestamps = true;
	public $incrementing = false;
	protected $table = "system_logs";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'type',
			'message',
			'posted_by',
			'posted_by_name',
			'posted_by_email',
			'loggable_id',
			'loggable_uuid',
			'loggable_id',
			'loggable_type',
		];
	protected $casts =
		[
			'type' => SystemLogType::class,
		];

	public function postedBy(): BelongsTo
	{
		return $this->belongsTo(Person::class, 'posted_by');
	}

	public function loggable(): MorphTo
	{
		return $this->morphTo();
	}

	#[Scope]
	protected function ofType(Builder $query, SystemLogType $type): void
	{
		$query->where('type', $type);
	}
}
