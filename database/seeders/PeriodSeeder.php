<?php

namespace Database\Seeders;

use App\Classes\Days;
use App\Models\Locations\Campus;
use App\Models\Schedules\Period;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PeriodSeeder extends Seeder
{
    public string $start = "08:00";
    public string $end = "15:00";
    public int $duration = 50;
    public int $between = 10;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(Campus::all() as $campus)
        {
            foreach(Days::weekdaysOptions() as $dayId => $dayName)
            {
                $idx = 1;
                $start = Carbon::createFromFormat('H:i', $this->start);
                $end = Carbon::createFromFormat('H:i', $this->end);
                while ($start->lt($end))
                {
                    Period::create(
                        [
                            'name' => $dayName . " " . $idx,
                            'abbr' => Days::dayAbbr($dayId) . $idx,
                            'day' => $dayId,
                            'start' => $start->format('H:i'),
                            'end' => $start->addMinutes($this->duration)->format('H:i'),
                            'campus_id' => $campus->id,
                        ]);
                    $start->addMinutes($this->between);
                    $idx++;
                }
            }
        }
    }
}
