<?php

namespace App\Classes\Synths;

use App\Casts\Learning\Rubric;
use App\Interfaces\Synthesizable;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class LmsSynth extends Synth
{
	
	public static $key = "lms-synth";
	
	public static function match($target)
	{
		return $target instanceof Synthesizable;
	}
	
	public function dehydrate($target)
	{
        $data = $target->toArray();
        $data['className'] = $target::class;
		return [
			$data
			, []];
	}
	
	public function hydrate($value): Synthesizable
	{
        $className = $value['className'];
        unset($value['className']);
		return $className::hydrate($value);
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
