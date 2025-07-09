<?php

namespace App\Classes\IdCard;

use App\Models\People\Person;
use Illuminate\Support\Facades\Blade;

class SchoolId extends IdCardElement
{

	public function __construct()
    {
        $this->config = static::$configDefaults['typography'];
    }

	public static function getName(): string
	{
		return trans_choice('people.id', 1);
	}

	public function render(Person $person): string
	{
        return '<div style="width: 100%;' . $this->typographyStyle() . '">' . $person->school_id . '</div>';
	}

	public function renderDummy(): string
	{
        return '<div style="width: 100%;' . $this->typographyStyle() . '">0000000000</div>';
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
        $schoolId = new SchoolId();
        $schoolId->colSpan = $data['colspan'];
        $schoolId->rowSpan = $data['rowspan'];
        $schoolId->config = $data['config'];
        return $schoolId;
	}
}
