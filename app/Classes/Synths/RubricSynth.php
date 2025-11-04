<?php

namespace App\Classes\Synths;

use App\Casts\Learning\Rubric;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class RubricSynth extends Synth
{
	
	public static $key = "rubric";
	
	public static function match($target)
	{
		return $target instanceof Rubric;
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
	
	public function get(&$target, $key)
	{
		return $target->{$key};
	}
	
	public function set(&$target, $key, $value)
	{
		$target->{$key} = $value;
	}
}
