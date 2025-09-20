<?php

namespace App\Classes\IdCard;

use App\Models\People\Person;
use Illuminate\Support\Facades\Blade;

class CustomText extends IdCardElement
{
	
	public function __construct()
	{
		$this->config = array_merge(static::$configDefaults['typography'], static::$configDefaults['custom-text']);
	}
	
	public static function getName(): string
	{
		return __('people.id.text.custom');
	}
	
	public static function hydrate(array $data): IdCardElement
	{
		$customText = new CustomText();
		$customText->colSpan = $data['colspan'];
		$customText->rowSpan = $data['rowspan'];
		$customText->config = $data['config'];
		return $customText;
	}
	
	public function renderDummy(): string
	{
		return '<div style="width: 100%;' . $this->typographyStyle() . '">' . ($this->config['custom-text'] ?? '') . '</div>';
	}
	
	public function controlComponent(): string
	{
		return
			"<ul class='list-group list-group-flush'>" .
			Blade::render(parent::$configViewFragments['custom-text'], ['element' => $this]) .
			Blade::render(parent::$configViewFragments['typography'], ['element' => $this]) .
			"</ul>";
	}
	
	public function render(Person $person): string
	{
		return '<div style="width: 100%;' . $this->typographyStyle() . '">' . ($this->config['custom-text'] ?? '') . '</div>';
	}
}
