<?php

namespace App\Classes\Synths;

use App\Classes\Integrators\Local\ClassManagement\ClassWidget;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class ClassWidgetSynth extends Synth
{
	
	public static $key = "classWidget";
	
	public static function match($target)
	{
		return $target instanceof ClassWidget;
	}
	
	public function dehydrate($target)
	{
		return [$target->toArray(), []];
	}
	
	public function hydrate($value): ClassWidget
	{
		$className = $value['className'];
		return $className::hydrate($value);
	}
}
