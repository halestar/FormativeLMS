<?php

namespace App\Classes\IdCard;

use App\Interfaces\Synthesizable;
use App\Models\People\Person;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

abstract class IdCardElement implements Synthesizable
{
	protected static array $configDefaults =
		[
			'typography' =>
				[
					'font-size' => '1',
					'italics' => false,
					'bold' => false,
					'underline' => false,
					'color' => '#000000',
					'text-align' => 'start',
					'font-family' => 'Arial',
					'text-shadow' => false,
					'text-shadow-x' => 2,
					'text-shadow-y' => 2,
					'text-shadow-blur' => 2,
					'text-shadow-color' => '#000000',
				],
			'images' =>
				[
					'border-radius' => 0,
					'border' => false,
					'border-color' => '#000000',
					'border-width' => 1,
					'border-style' => 'solid',
					'img-padding' => 0,
					'img-margin' => 0,
					'img-shadow' => false,
					'img-shadow-x' => 2,
					'img-shadow-y' => 2,
					'img-shadow-blur' => 2,
					'img-shadow-color' => '#000000',
				],
			'custom-text' =>
				[
					'custom-text' => '',
				],
			'barcode' =>
				[
					'barcode-type' => 'code-128',
					'barcode-scale-x' => '1',
					'barcode-scale-y' => '1',
					'barcode-padding' => '20',
					'barcode-bg-color' => '#ffffff',
					'barcode-transparent-bg' => false,
					'barcode-space-color' => '#ffffff',
					'barcode-transparent-space' => false,
					'barcode-bar-color' => '#000000',
					'barcode-text-color' => '#000000',
					'barcode-text-size' => '10',
					'barcode-text-font' => 'monospace',
				],
		];
	
	protected static array $configViewFragments =
		[
			'typography' => 'livewire.people.id-creator.typography-element',
			'images' => 'livewire.people.id-creator.image-element',
			'custom-text' => 'livewire.people.id-creator.custom-text-element',
			'barcode' => 'livewire.people.id-creator.barcode-element',
		];
	public int $colSpan = 1;
	public int $rowSpan = 1;
	protected ?string $value = null;
	protected array $config = [];
	
	public abstract function __construct();
	
	public abstract static function getName(): string;
	
	public abstract function render(Person $person): string;
	
	public abstract function renderDummy(): string;
	
	public abstract function controlComponent(): string;
	
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}
	
	public function toArray(): array
	{
		return
			[
				'colspan' => $this->colSpan,
				'rowspan' => $this->rowSpan,
				'config' => $this->config,
			];
	}
	
	public function getConfig(string $key, mixed $default = null): mixed
	{
		return $this->config[$key] ?? $default;
	}
	
	public function setConfig(string $key, mixed $value): void
	{
		$this->config[$key] = $value;
	}
	
	protected function typographyStyle(): string
	{
		$style = ';font-size: ' . $this->config['font-size'] . 'em;';
		if(isset($this->config['italics']) && $this->config['italics'])
			$style .= 'font-style: italic;';
		if(isset($this->config['bold']) && $this->config['bold'])
			$style .= 'font-weight: bold;';
		if(isset($this->config['underline']) && $this->config['underline'])
			$style .= 'text-decoration: underline;';
		if(isset($this->config['color']) && $this->config['color'])
			$style .= 'color: ' . $this->config['color'] . ';';
		if(isset($this->config['text-align']))
			$style .= 'text-align: ' . $this->config['text-align'] . ';';
		if(isset($this->config['font-family']))
			$style .= 'font-family: ' . $this->config['font-family'] . ',sans-serif;';
		if(isset($this->config['text-shadow']) && $this->config['text-shadow'])
			$style .= 'text-shadow: ' . $this->config['text-shadow-x'] . 'px ' . $this->config['text-shadow-y'] . 'px ' . $this->config['text-shadow-blur'] . 'px ' . $this->config['text-shadow-color'] . ';';
		return $style;
	}
	
	protected function imageStyle(): string
	{
		$style = "";
		if(isset($this->config['border-radius']))
			$style .= 'border-radius: ' . $this->config['border-radius'] . '%;';
		if(isset($this->config['border']) && $this->config['border'])
		{
			$style .= "border-width: " . ($this->config['border-width'] ?? "1") . "px;";
			$style .= "border-style: " . ($this->config['border-style'] ?? "solid") . ";";
			$style .= "border-color: " . ($this->config['border-color'] ?? "#000000") . ";";
		}
		if(isset($this->config['img-padding']))
			$style .= 'padding: ' . $this->config['img-padding'] . 'px;';
		if(isset($this->config['img-margin']))
			$style .= 'margin: ' . $this->config['img-margin'] . 'px;';
		if(isset($this->config['img-shadow']) && $this->config['img-shadow'])
		{
			$style .= "box-shadow: " . $this->config['img-shadow-x'] . "px " . $this->config['img-shadow-y'] .
				"px " . $this->config['img-shadow-blur'] . "px " . $this->config['img-shadow-color'] . ";";
		}
		return $style;
	}
	
	protected function barcodeStyle(BarcodeGenerator $barcode): BarcodeGenerator
	{
		if(isset($this->config['barcode-type']))
			$barcode->barcodeType = $this->config['barcode-type'];
		if(isset($this->config['barcode-scale-x']))
			$barcode->horizontalScaleFactor = $this->config['barcode-scale-x'];
		if(isset($this->config['barcode-scale-y']))
			$barcode->verticalScaleFactor = $this->config['barcode-scale-y'];
		if(isset($this->config['barcode-padding']))
			$barcode->padding = $this->config['barcode-padding'];
		if(isset($this->config['barcode-transparent-bg']))
			$barcode->backgroundColor = $this->config['barcode-transparent-bg'] ? null : $this->config['barcode-bg-color'];
		if(isset($this->config['barcode-transparent-space']))
			$barcode->spaceColor = $this->config['barcode-transparent-space'] ? null : $this->config['barcode-space-color'];
		if(isset($this->config['barcode-bar-color']))
			$barcode->moduleColor = $this->config['barcode-bar-color'];
		if(isset($this->config['barcode-text-color']))
			$barcode->textColor = $this->config['barcode-text-color'];
		if(isset($this->config['barcode-text-size']))
			$barcode->textSize = $this->config['barcode-text-size'];
		if(isset($this->config['barcode-text-font']))
			$barcode->textFont = $this->config['barcode-text-font'];
		return $barcode;
	}
}
