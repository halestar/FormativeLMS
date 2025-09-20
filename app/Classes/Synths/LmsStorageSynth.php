<?php

namespace App\Classes\Synths;

use App\Classes\Storage\LmsStorage;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class LmsStorageSynth extends Synth
{
	
	public static $key = "lms-storage";
	
	public static function match($target)
	{
		return $target instanceof LmsStorage;
	}
	
	public function dehydrate($target)
	{
		return [
			$target->toArray()
			, []];
	}
	
	public function hydrate($value): mixed
	{
		return LmsStorage::hydrate($value);
	}
}
