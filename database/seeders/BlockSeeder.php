<?php

namespace Database\Seeders;

use App\Classes\Days;
use App\Models\Locations\Campus;
use App\Models\Schedules\Block;
use Illuminate\Database\Seeder;

class BlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(Campus::all() as $campus)
        {
            foreach(['A', 'B', 'C', 'D', 'E'] as $blockName)
            {
                $periods = [];
                foreach(Days::getWeekdays() as $day)
                {
                    if($campus->periods($day)->count() > 0)
                        $periods[] = $campus->periods($day)->inRandomOrder()->first()->id;
                }
                $block = Block::create(
                    [
                        'name' => $blockName,
                        'campus_id' => $campus->id,
                    ]);
                $block->periods()->attach($periods);
            }
        }
    }
}
