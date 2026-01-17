<?php

namespace App\Http\Resources\Locations;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildingAreaResource extends JsonResource
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
				'blueprint_url' => $this->blueprint_url?->__toString(),
				'rooms' => RoomResource::collection($this->rooms),
			];
	}
}
