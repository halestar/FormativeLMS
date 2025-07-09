<?php

namespace App\Classes\IdCard;

use App\Models\People\Person;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;

class SchoolIdBarcode extends IdCardElement
{

	public function __construct()
    {
        $this->config = static::$configDefaults['barcode'];
    }

	public static function getName(): string
	{
        return __('people.id.barcode');
	}

    private function barcodeElement(BarcodeGenerator $barcode): string
    {
        $this->barcodeStyle($barcode);
        Log::debug(print_r($barcode, true));
        return "<div class='m-auto'>" . $barcode->toSVG() . "</div>";
    }

	public function render(Person $person): string
	{
        $barcode = new BarcodeGenerator($person->school_id);
        return $this->barcodeElement($barcode);
	}

	public function renderDummy(): string
	{
		$barcode = new BarcodeGenerator("0000000000");
        return $this->barcodeElement($barcode);
	}

	public function controlComponent(): string
	{
        return
            "<ul class='list-group list-group-flush'>" .
            Blade::render(parent::$configViewFragments['barcode'], ['element' => $this]) .
            "</ul>";
	}

	public static function hydrate(array $data): IdCardElement
	{
        $barcode = new SchoolIdBarcode();
        $barcode->colSpan = $data['colspan'];
        $barcode->rowSpan = $data['rowspan'];
        $barcode->config = $data['config'];
        return $barcode;
	}
}
