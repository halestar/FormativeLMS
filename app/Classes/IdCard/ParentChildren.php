<?php

namespace App\Classes\IdCard;

use App\Models\People\Person;
use Illuminate\Support\Facades\Blade;

class ParentChildren extends IdCardElement
{
	public static function getName(): string
	{
		return __('people.id.parents.children');
	}

	public function render(Person $person): string
	{
        if($person->isParent())
        {
            $student = $person->currentChildStudents();
            $children = [];
            foreach($student as $child)
                $children[] = $child->person->name . " (" . $child->level->name . ")";
            $children = implode(", ", $children);
        }
        else
            $children = __('people.id.parents.children.no');
        return '<div style="width: 100%;' . $this->typographyStyle() . '">' . $children . '</div>';
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
		$personName = new ParentChildren();
        $personName->colSpan = $data['colspan'];
        $personName->rowSpan = $data['rowspan'];
        $personName->config = $data['config'];
        return $personName;
	}

    public function __construct()
    {
        $this->config = static::$configDefaults['typography'];
    }

    public function renderDummy(): string
    {
        $dummy = '<div style="width: 100%;' . $this->typographyStyle() . '">Child (grade)</div>';
        return $dummy;
    }
}
