<?php

namespace App\Models\SubjectMatter\Learning;

use App\Classes\Learning\GradeTranslationTable;
use App\Models\Locations\Campus;
use App\Models\Locations\Year;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeTranslationSchema extends Model
{
	public $timestamps = true;
	public $incrementing = true;
	protected $table = "grade_translation_schemas";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'campus_id',
			'year_id',
			'show_opportunity_grade',
			'translate_opportunity_grade',
			'show_criteria_grade',
			'translate_criteria_grade',
			'show_overall_grade',
			'translate_overall_grade',
			'grade_translations',
		];
	
	protected function casts()
	{
		return
		[
			'show_opportunity_grade' => 'boolean',
			'translate_opportunity_grade' => 'boolean',
			'show_criteria_grade' => 'boolean',
			'translate_criteria_grade' => 'boolean',
			'show_overall_grade' => 'boolean',
			'translate_overall_grade' => 'boolean',
			'grade_translations' => GradeTranslationTable::class,
		];
	}
	
	public function campus(): BelongsTo
	{
		return $this->belongsTo(Campus::class, 'campus_id');
	}
	
	public function year(): BelongsTo
	{
		return $this->belongsTo(Year::class, 'year_id');
	}
	
}
