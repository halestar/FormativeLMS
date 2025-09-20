<?php

namespace App\Classes\Synths;

use App\Classes\Auth\AuthenticationDesignation;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class AuthenticationDesignationSynth extends Synth
{
	public static $key = "authentication-designation";
	
	public static function match($target)
	{
		return $target instanceof AuthenticationDesignation;
	}
	
	public function dehydrate($target)
	{
		return [
			$target->toArray()
			, []];
	}
	
	public function hydrate($value): AuthenticationDesignation
	{
		return AuthenticationDesignation::hydrate($value);
	}
}
