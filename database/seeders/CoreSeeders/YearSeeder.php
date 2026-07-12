<?php

namespace Database\Seeders\CoreSeeders;

use App\Models\Locations\Campus;
use App\Models\Locations\Term;
use App\Models\Locations\Year;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class YearSeeder extends Seeder
{
	protected int $termsPerYear;
	protected string $schoolYearStarts;

	public function __construct()
	{
		$this->termsPerYear = config('lms.terms_per_year');
		$this->schoolYearStarts = config('lms.school_year_starts');
	}

	protected function createTerms(Year $year): void
	{
		$numTerms = $this->termsPerYear;
		$numMonths = (int)(12 / $numTerms);
		foreach (Campus::all() as $campus)
		{
			$termStart = $year->year_start->copy();
			$yearEnd = $year->year_end->format('Y-m-d');
			for ($i = 0; $i < $numTerms; $i++)
			{
				$start = $termStart->format('Y-m-d');
				$end = $termStart->addMonths($numMonths)->format('Y-m-d');
				if ($i == ($numTerms - 1))
					$end = $yearEnd;
				Term::create(
					[
						'campus_id' => $campus->id,
						'year_id' => $year->id,
						'label' => 'Term ' . ($i + 1),
						'term_start' => $start,
						'term_end' => $end,
					]
				);
			}
		}
	}

	protected function createYear(Carbon $yearStart): void
	{
		$yearEnd = $yearStart->copy();
		$yearEnd->addYears(1)->subDay();
		$yearLabel = ($yearEnd->year == $yearStart->year) ?
			$yearStart->format('Y') :
			$yearStart->format('Y') . " - " . $yearEnd->format('Y');
		$newYear = Year::create(
			[
				'label' => $yearLabel,
				'year_start' => $yearStart->format('Y-m-d'),
				'year_end' => $yearEnd->format('Y-m-d'),
			]
		);
		//create the semesters
		$this->createTerms($newYear);
	}

	protected function createCurrentYear(): void
	{
		$currentYear = Carbon::parse(date('Y') . "-" . $this->schoolYearStarts);
		if ($currentYear->isFuture())
			$currentYear->subYear();
		$this->createYear($currentYear);
	}

	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$this->createCurrentYear();
	}
}
