<?php

namespace App\Classes\Synths;

use App\Classes\NameToken;
use App\Classes\RoleField;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class NameTokenSynth extends Synth
{
	
	public static $key = "nameToken";
	
	public static function match($target)
	{
		return $target instanceof NameToken;
	}
	
	public function dehydrate($target)
	{
		return [[
			        'type' => $target->type,
			        'textContent' => $target->textContent,
			        'basicFieldName' => $target->basicFieldName,
			        'roleField' => $target->roleField ? $target->roleField->toArray() : null,
			        'spaceAfter' => $target->spaceAfter,
			        'roleId' => $target->roleId,
		        ], []];
	}
	
	public function hydrate($value)
	{
		$instance = new NameToken($value['type']);
		
		$instance->textContent = $value['textContent'];
		$instance->basicFieldName = $value['basicFieldName'];
		$instance->roleField = $value['roleField'] ? new RoleField($value['roleField']) : null;
		$instance->spaceAfter = $value['spaceAfter'];
		$instance->roleId = $value['roleId'];
		return $instance;
	}
}
