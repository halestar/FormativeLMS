<?php

namespace App\Classes\IdCard;

use App\Models\People\Person;
use Illuminate\Support\Facades\Blade;

class PersonPicture extends IdCardElement
{
	
	public function __construct()
	{
		$this->config = static::$configDefaults['images'];
	}
	
	public static function getName(): string
	{
		return "Person Picture";
	}
	
	public static function hydrate(array $data): static
	{
		$personPic = new PersonPicture();
		$personPic->colSpan = $data['colspan'];
		$personPic->rowSpan = $data['rowspan'];
		$personPic->config = $data['config'];
		return $personPic;
	}
	
	public function renderDummy(): string
	{
		return "<img class='img-fluid' style='" . $this->imageStyle() . "' src='" . Person::UKN_IMG . "' />";
	}
	
	public function controlComponent(): string
	{
		return
			"<ul class='list-group list-group-flush'>" .
			Blade::render(parent::$configViewFragments['images'], ['element' => $this]) .
			"</ul>";
	}
	
	public function render(Person $person): string
	{
		return "<img class='img-fluid' style='" . $this->imageStyle() . "' src='" . $person->portrait_url . "' />";
	}
}
