<?php

namespace Database\Seeders;

use App\Casts\Rubric;
use App\Models\CRUD\SkillCategoryDesignation;
use App\Models\Locations\Campus;
use App\Models\SubjectMatter\Assessment\KnowledgeSkill;
use App\Models\SubjectMatter\Assessment\SkillCategory;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class KnowledgeSkillSeeder extends Seeder
{
    public Campus $hs;
    public Campus $ms;
    public Campus $es;

    public function __construct()
    {
        $this->hs = Campus::where('abbr', 'HS')->first();
        $this->ms = Campus::where('abbr', 'MS')->first();
        $this->es = Campus::where('abbr', 'ES')->first();
    }
    public function importSkills(string $fname, array $subjects)
    {
        if($standards = fopen($fname, 'r'))
        {
            $faker = Container::getInstance()->make(Generator::class);
            //first, we get the headers
            $header = fgetcsv($standards);
            $contentArea = 0;
            $designation = 1;
            $gradeRange = 2;
            $minGrade = 3;
            $maxGrade = 4;
            $description = count($header) - 1;
            $categories = [];
            for($i = 5; $i < $description; $i++)
                $catDesignations[] = SkillCategoryDesignation::where('name', $header[$i])->first()->id;
            //next, we cycle through the data
            while($row = fgetcsv($standards))
            {
                //find the campus based on the starting grade
                $startGrade = $row[$minGrade] + 1;
                $endGrade = $row[$maxGrade] + 1;
                $subject_id = $startGrade <= 6 ? $subjects['es'] : ($startGrade <= 9 ? $subjects['ms'] : $subjects['hs']);
                $skill = KnowledgeSkill::create(
                    [
                        'subject_id' => $subject_id,
                        'designation' => $row[$designation],
                        'description' => nl2br($row[$description]),
                        'rubric' => null,
                        'active' => true,
                    ]);
                //next, we attach the grades
                for($i = $minGrade; $i <= $maxGrade; $i++)
                    $skill->levels()->attach($i);
                //and the categories
                $ctr = 5;
                foreach($catDesignations as $catDesignation)
                {
                    $cat = SkillCategory::where('name', $row[$ctr])->first();
                    if($cat && $catDesignation)
                        $cat->knowledgeSkills()->attach($skill->id, ['designation_id' => $catDesignation]);
                    else
                        Log::error('Could not find category: ' . $row[$ctr] . " or designation: " . $catDesignation);
                    $ctr++;
                }
            }
        }
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //English
        $subjects =
            [
                'hs' => $this->hs->subjects()->where('name', 'English')->first()->id,
                'ms' => $this->ms->subjects()->where('name', 'English')->first()->id,
                'es' => $this->es->subjects()->where('name', 'English')->first()->id,
            ];
        $this->importSkills(storage_path('app/standards/english.csv'), $subjects);
        //Math
        $subjects =
            [
                'hs' => $this->hs->subjects()->where('name', 'Math')->first()->id,
                'ms' => $this->ms->subjects()->where('name', 'Math')->first()->id,
                'es' => $this->es->subjects()->where('name', 'Math')->first()->id,
            ];
        $this->importSkills(storage_path('app/standards/math.csv'), $subjects);
        //Science
        $subjects =
            [
                'hs' => $this->hs->subjects()->where('name', 'Science')->first()->id,
                'ms' => $this->ms->subjects()->where('name', 'Science')->first()->id,
                'es' => $this->es->subjects()->where('name', 'Science')->first()->id,
            ];
        $this->importSkills(storage_path('app/standards/science.csv'), $subjects);
        //Social Studies
        $subjects =
            [
                'hs' => $this->hs->subjects()->where('name', 'Social Studies')->first()->id,
                'ms' => $this->ms->subjects()->where('name', 'Social Studies')->first()->id,
                'es' => $this->es->subjects()->where('name', 'Social Studies')->first()->id,
            ];
        $this->importSkills(storage_path('app/standards/social_science.csv'), $subjects);
        //Art
        $subjects =
            [
                'hs' => $this->hs->subjects()->where('name', 'Art')->first()->id,
                'ms' => $this->ms->subjects()->where('name', 'Art')->first()->id,
                'es' => $this->es->subjects()->where('name', 'Art')->first()->id,
            ];
        $this->importSkills(storage_path('app/standards/art.csv'), $subjects);
        //Health
        $subjects =
            [
                'hs' => $this->hs->subjects()->where('name', 'Health')->first()->id,
                'ms' => $this->ms->subjects()->where('name', 'Health')->first()->id,
                'es' => $this->es->subjects()->where('name', 'Health')->first()->id,
            ];
        $this->importSkills(storage_path('app/standards/health.csv'), $subjects);
        //Languages
        $subjects =
            [
                'hs' => $this->hs->subjects()->where('name', 'Languages')->first()->id,
                'ms' => $this->ms->subjects()->where('name', 'Languages')->first()->id,
                'es' => $this->es->subjects()->where('name', 'Languages')->first()->id,
            ];
        $this->importSkills(storage_path('app/standards/language.csv'), $subjects);

    }
}
