<?php

namespace Database\Seeders\SimpleSeeder;

use Illuminate\Support\Carbon;

class YearSeeder extends \Database\Seeders\CoreSeeders\YearSeeder
{

	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		// For the purpose of this seed, all school years begin on Aug 1st
		// and end on July 31st
		$this->termsPerYear = 2;
		$this->schoolYearStarts = '08-01';
		$currentYear = Carbon::parse(date('Y') . "-" . $this->schoolYearStarts);
		if ($currentYear->isAfter(Carbon::now()))
			$currentYear = $currentYear->subYear();
		for ($i = 0; $i < 20; $i++)
		{
			$this->createYear($currentYear);
			$currentYear = $currentYear->addYear();
			$i++;
		}
	}


}
