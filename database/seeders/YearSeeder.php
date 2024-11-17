<?php

namespace Database\Seeders;

use App\Models\Locations\Campus;
use App\Models\Locations\Term;
use App\Models\Locations\Year;
use Illuminate\Database\Seeder;

class YearSeeder extends Seeder
{

    protected function createTerms(Year $year): void
    {
        foreach(Campus::all()->toArray() as $campus)
        {
            Term::create(
                    [
                        'campus_id' => $campus['id'],
                        'year_id' => $year->id,
                        'label' => 'Fall Semester',
                        'term_start' => $year->year_start->format('Y-m-d'),
                        'term_end' => $year->year_start->format('Y') . "-12-31",
                    ]);
            Term::create(
                    [
                        'campus_id' => $campus['id'],
                        'year_id' => $year->id,
                        'label' => 'Spring Semester',
                        'term_start' => $year->year_end->format('Y') . "-01-01",
                        'term_end' => $year->year_end->format('Y') . "-06-10",
                    ]);
        }
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // For the purpose of this seed, all school years begin on Aug 1st
        // and end on July 31st
        $currentYear = date('Y');
        $currentMonth = date('n');
        if($currentMonth <= 7)
        {
            $yearStart = ((int)$currentYear - 1) . "-08-01";
            $yearEnd = $currentYear . "-07-31";
            $yearLabel = ((int)$currentYear - 1) . " - " . $currentYear;
            $nextYear = $currentYear;
        }
        else
        {
            $yearStart = $currentYear . "-08-01";
            $yearEnd = ((int)$currentYear + 1) . "-07-31";
            $yearLabel = $currentYear . " - " . ((int)$currentYear + 1);
            $nextYear = (int)$currentYear + 1;
        }
        $newYear = Year::create(
            [
                'label' => $yearLabel,
                'year_start' => $yearStart,
                'year_end' => $yearEnd,
            ]);
        //create the semesters
        $this->createTerms($newYear);

        //next, we will make the next 20 years.
        for($year = $nextYear, $i = 0; $i < 20; $year++, $i++)
        {
            $newYear = Year::create(
                [
                    'label' => $year . " - " . ((int)$year + 1),
                    'year_start' => $year . "-08-01",
                    'year_end' => ((int)$year + 1) . "-07-31",
                ]);
            $this->createTerms($newYear);
        }
    }
}
