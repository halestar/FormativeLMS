<?php

namespace App\Classes\Synths;

use App\Casts\Learning\Rubric;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class GradeTranslationTableSynth extends Synth
{
	
	public static $key = "grade-translation-table";
	
	public static function match($target)
	{
		return $target instanceof GradeTranslationTableSynth;
	}
	
	public function dehydrate($target)
	{
		return [
			$target->toArray()
			, []];
	}
	
	public function hydrate($value): Rubric
	{
		return Rubric::hydrate($value);
	}
}
