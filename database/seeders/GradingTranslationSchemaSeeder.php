<?php

namespace Database\Seeders;

use App\Classes\Learning\GradeTranslationTable;
use App\Models\Locations\Campus;
use App\Models\Locations\Year;
use App\Models\SubjectMatter\Learning\GradeTranslationSchema;
use Illuminate\Database\Seeder;

class GradingTranslationSchemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		$campuses = Campus::all();
		$year = Year::currentYear();
		foreach($campuses as $campus)
		{
			GradeTranslationSchema::create(
				[
					'campus_id' => $campus->id,
					'year_id' => $year->id,
					'show_opportunity_grade' => 1,
					'translate_opportunity_grade' => 1,
					'show_criteria_grade' => 1,
					'translate_criteria_grade' => 1,
					'show_overall_grade' => 1,
					'translate_overall_grade' => 1,
					'grade_translations' => new GradeTranslationTable(json_decode('[{"max": 60, "min": 0, "grade": "F", "appliesToOverall": true, "appliesToReports": true, "appliesToCriteria": true, "appliesToTranscripts": true, "appliesToOpportunities": true}, {"max": 70, "min": 60, "grade": "D", "appliesToOverall": true, "appliesToReports": true, "appliesToCriteria": true, "appliesToTranscripts": true, "appliesToOpportunities": true}, {"max": 80, "min": 70, "grade": "C", "appliesToOverall": true, "appliesToReports": true, "appliesToCriteria": true, "appliesToTranscripts": true, "appliesToOpportunities": true}, {"max": 90, "min": 80, "grade": "B", "appliesToOverall": true, "appliesToReports": true, "appliesToCriteria": true, "appliesToTranscripts": true, "appliesToOpportunities": true}, {"max": 100, "min": 90, "grade": "A", "appliesToOverall": true, "appliesToReports": true, "appliesToCriteria": true, "appliesToTranscripts": true, "appliesToOpportunities": true}]', true)),
				]);
		}
	    
    }
}
