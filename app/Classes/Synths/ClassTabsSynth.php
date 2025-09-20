<?php

namespace App\Classes\Synths;

use App\Classes\ClassManagement\ClassTabs;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class ClassTabsSynth extends Synth
{
	
	public static $key = "classTabs";
	
	public static function match($target)
	{
		return $target instanceof ClassTabs;
	}
	
	public function dehydrate($target)
	{
		return [
			$target->toArray()
			, []];
	}
	
	public function hydrate($value): ClassTabs
	{
		return ClassTabs::hydrate($value);
	}
}
