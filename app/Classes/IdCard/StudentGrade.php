<?php

namespace App\Classes\IdCard;

use App\Models\People\Person;
use Illuminate\Support\Facades\Blade;

class StudentGrade extends IdCardElement
{
	public function __construct()
	{
		$this->config = static::$configDefaults['typography'];
	}
	
	public static function getName(): string
	{
		return __('people.id.student.grade');
	}
	
	public static function hydrate(array $data): static
	{
		$personName = new StudentGrade();
		$personName->colSpan = $data['colspan'];
		$personName->rowSpan = $data['rowspan'];
		$personName->config = $data['config'];
		return $personName;
	}
	
	public function controlComponent(): string
	{
		return
			"<ul class='list-group list-group-flush'>" .
			Blade::render(parent::$configViewFragments['typography'], ['element' => $this]) .
			"</ul>";
	}
	
	public function render(Person $person): string
	{
		if($person->isStudent())
			$grade = $person->student()->level->name;
		else
			$grade = __('common.unknown');
		return '<div style="width: 100%;' . $this->typographyStyle() . '">' . $grade . '</div>';
	}
	
	public function renderDummy(): string
	{
		$dummy = '<div style="width: 100%;' . $this->typographyStyle() . '">' . __('people.id.student.grade') . '</div>';
		return $dummy;
	}
}
