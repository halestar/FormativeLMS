<?php

namespace App\Classes\IdCard;

use App\Models\People\Person;
use Illuminate\Support\Facades\Blade;

class PersonName extends IdCardElement
{
	public function __construct()
	{
		$this->config = static::$configDefaults['typography'];
	}
	
	public static function getName(): string
	{
		return __('people.id.person.name');
	}
	
	public static function hydrate(array $data): IdCardElement
	{
		$personName = new PersonName();
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
		return '<div style="width: 100%;' . $this->typographyStyle() . '">' . $person->name . '</div>';
	}
	
	public function renderDummy(): string
	{
		$dummy = '<div style="width: 100%;' . $this->typographyStyle() . '">Person Name</div>';
		return $dummy;
	}
}
