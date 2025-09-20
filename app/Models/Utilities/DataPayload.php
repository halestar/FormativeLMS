<?php

namespace App\Models\Utilities;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DataPayload extends Model
{
	use HasUuids;
	
	public $timestamps = false;
	public $incrementing = false;
	protected $table = "data_payloads";
	protected $primaryKey = "id";
	protected $keyType = 'string';
	
	public static function createPayload(array $payload): DataPayload
	{
		$payload = new DataPayload();
		$payload->payload = $payload;
		$payload->id = Str::uuid()
		                  ->toString();
		$payload->save();
		return $payload;
	}
	
	
	protected function casts(): array
	{
		return
			[
				'payload' => 'array',
			];
	}
}
