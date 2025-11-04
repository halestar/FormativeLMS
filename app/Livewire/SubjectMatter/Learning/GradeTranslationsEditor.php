<?php

namespace App\Livewire\SubjectMatter\Learning;

use App\Classes\Learning\GradeTranslationRow;
use App\Classes\Learning\GradeTranslationTable;
use App\Classes\SessionSettings;
use App\Models\Locations\Campus;
use App\Models\Locations\Year;
use App\Models\SubjectMatter\Learning\GradeTranslationSchema;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class GradeTranslationsEditor extends Component
{
	public Campus $campus;
	public Collection $years;
	public int $selectedYearId;
	public ?GradeTranslationSchema $schema;
	public bool $show_opportunity_grade;
	public bool $translate_opportunity_grade;
	public bool $show_criteria_grade;
	public bool $translate_criteria_grade;
	public bool $show_overall_grade;
	public bool $translate_overall_grade;
	public Collection $importFrom;
	public ?int $selectedImportFrom;
	
	public function mount(Campus $campus, SessionSettings $sessionSettings)
	{
		$this->campus = $campus;
		$this->years = Year::all();
		$this->selectedYearId = $sessionSettings->workingYear()->id;
		$this->schema = $campus->gradeTranslationSchema($sessionSettings->workingYear())->first();
		$this->updateProperties();
	}
	
	public function updateProperties()
	{
		$this->show_overall_grade = $this->schema?->show_overall_grade ?? true;
		$this->translate_overall_grade = $this->schema?->translate_overall_grade ?? false;
		$this->show_criteria_grade = $this->schema?->show_criteria_grade ?? true;
		$this->translate_criteria_grade = $this->schema?->translate_criteria_grade ?? false;
		$this->show_opportunity_grade = $this->schema?->show_opportunity_grade ?? true;
		$this->translate_opportunity_grade = $this->schema?->translate_opportunity_grade ?? false;
		$this->importFrom = GradeTranslationSchema::all();
		$this->selectedImportFrom = $this->importFrom->first()?->id;
	}
	
	public function saveProperties()
	{
		$this->schema->show_overall_grade = $this->show_overall_grade;
		$this->schema->translate_overall_grade = $this->translate_overall_grade;
		$this->schema->show_criteria_grade = $this->show_criteria_grade;
		$this->schema->translate_criteria_grade = $this->translate_criteria_grade;
		$this->schema->show_opportunity_grade = $this->show_opportunity_grade;
		$this->schema->translate_opportunity_grade = $this->translate_opportunity_grade;
		$this->schema->save();
	}
	
	public function setYear()
	{
		$year = Year::find($this->selectedYearId);
		$this->schema = $this->campus->gradeTranslationSchema($year)->first();
		$this->updateProperties();
	}
	
	public function create()
	{
		$year = Year::find($this->selectedYearId);
		if(!$this->campus->gradeTranslationSchema($year)->exists())
		{
			$schema = new GradeTranslationSchema();
			$schema->campus_id = $this->campus->id;
			$schema->year_id = $year->id;
			$schema->show_overall_grade = $this->show_overall_grade;
			$schema->translate_overall_grade = $this->translate_overall_grade;
			$schema->show_criteria_grade = $this->show_criteria_grade;
			$schema->translate_criteria_grade = $this->translate_criteria_grade;
			$schema->show_opportunity_grade = $this->show_opportunity_grade;
			$schema->translate_opportunity_grade = $this->translate_opportunity_grade;
			$schema->grade_translations = new GradeTranslationTable();
			$schema->save();
		}
		$this->schema = $this->campus->gradeTranslationSchema($year)->first();
		$this->updateProperties();
	}
	
	public function import()
	{
		$toYear = Year::find($this->selectedYearId);
		if(!$this->campus->gradeTranslationSchema($toYear)->exists())
		{
			$fromSchema = GradeTranslationSchema::find($this->selectedImportFrom);
			$toSchema = $fromSchema->replicate()->fill(['year_id' => $toYear->id, 'campus_id' => $this->campus->id]);
			$toSchema->save();
		}
		$this->schema = $this->campus->gradeTranslationSchema($toYear)->first();
		$this->updateProperties();
	}
	
	public function addRow()
	{
		$this->schema->grade_translations->addRow();
		$this->schema->save();
	}
	
	public function removeRow($index)
	{
		$this->schema->grade_translations->removeRow($index);
		$this->schema->save();
	}
	
	public function updateRow($index, $properties)
	{
		Log::debug('properties: ' . print_r($properties, true));
		$data = Validator::validate($properties,
			[
				'min' => 'required|numeric',
				'max' => 'required|numeric',
				'grade'=> 'required',
				'appliesToOpportunities' => 'required|boolean',
				'appliesToCriteria' => 'required|boolean',
				'appliesToTranscripts' => 'required|boolean',
				'appliesToReports' => 'required|boolean',
				'appliesToOverall' => 'required|boolean',
			]);
		Log::debug('data: ' . print_r($data, true));
		$this->schema->grade_translations->updateRow($index, new GradeTranslationRow($data));
		$this->schema->save();
	}
	
    public function render()
    {
		
	    return view('livewire.subject-matter.learning.grade-translations-editor')
		    ->with('grade_translations', $this->schema?->grade_translations);
    }
}
