<?php

namespace App\Http\Resources\Locations;

use App\Http\Resources\People\PhoneResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
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
				'name' => $this->name,
				'capacity' => $this->capacity,
				'img_data' => $this->img_data,
				'phone' => new PhoneResource($this->phone),
				'created_at' => $this->created_at,
				'updated_at' => $this->updated_at,
			];
	}
}
