<?php

namespace App\Classes\IdCard;

use App\Models\Locations\Year;
use App\Models\People\Person;
use Illuminate\Support\Facades\Blade;

class SchoolYear extends IdCardElement
{

	public function __construct()
    {
        $this->config = static::$configDefaults['typography'];
    }

	public static function getName(): string
	{
		return __('people.id.year');
	}

	public function render(Person $person): string
	{
        $year = Year::currentYear();
        return '<div style="width: 100%;' . $this->typographyStyle() . '">' . $year->label . '</div>';
	}

	public function renderDummy(): string
	{
        $year = Year::currentYear();
        return '<div style="width: 100%;' . $this->typographyStyle() . '">' . $year->label . '</div>';
	}

	public function controlComponent(): string
	{
        return
            "<ul class='list-group list-group-flush'>" .
            Blade::render(parent::$configViewFragments['typography'], ['element' => $this]) .
            "</ul>";
	}

	public static function hydrate(array $data): IdCardElement
	{
        $schoolYear = new SchoolYear();
        $schoolYear->colSpan = $data['colspan'];
        $schoolYear->rowSpan = $data['rowspan'];
        $schoolYear->config = $data['config'];
        return $schoolYear;
	}
}
