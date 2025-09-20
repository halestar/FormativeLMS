<?php

namespace App\Http\Resources\People;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhoneResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return
			[
				'id' => $this->id,
				'phone' => $this->phone,
				'ext' => $this->ext,
				'pretty' => $this->pretty_phone,
				'mobile' => $this->mobile,
				'created_at' => $this->created_at,
				'updated_at' => $this->updated_at,
			];
	}
}
