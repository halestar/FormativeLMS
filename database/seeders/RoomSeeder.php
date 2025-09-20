<?php

namespace Database\Seeders;

use App\Models\Locations\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		
		//free floating rooms
		Room::factory()
		    ->count(3)
		    ->hasCampuses()
		    ->create();
		
	}
}
