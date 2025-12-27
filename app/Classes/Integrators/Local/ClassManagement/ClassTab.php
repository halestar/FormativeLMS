<?php

namespace App\Classes\Integrators\Local\ClassManagement;

use App\Classes\Integrators\Local\LocalIntegrator;
use App\Enums\IntegratorServiceTypes;
use App\Interfaces\Synthesizable;
use Livewire\Component;

class ClassTab implements Synthesizable
{
	public string $name;
	public array $widgets;
	private string $id;
	
	public function __construct(string $name)
	{
		$this->name = $name;
		$this->id = uniqid();
		$this->widgets = [];
	}
	
	public static function hydrate(array $data): static
	{
		$tab = new ClassTab($data['name']);
		$tab->id = $data['id'];
		$tab->widgets = $data['widgets'];
		return $tab;
	}

	public function addWidget(string $widget): void
	{
		$this->widgets[] = $widget;
	}
	
	public function removeWidget(string $widget): void
	{
		$this->widgets = array_values(array_diff($this->widgets, [$widget]));
	}

	public function hasWidget(string $widget): bool
	{
		return in_array($widget, $this->widgets);
	}

	public function canRemoveWidget(string $widget): bool
	{
		//we check if the widget is in the required array
		$classesService = LocalIntegrator::getService(IntegratorServiceTypes::CLASSES);
		return in_array($widget, $classesService->data->required);
	}

	public function canDelete(): bool
	{
		foreach($this->widgets as $widget)
			if(!$this->canRemoveWidget($widget)) return false;
		return true;
	}
	
	public function getId(): string
	{
		return $this->id;
	}
	
	public function toArray(): array
	{
		return
		[
			'name' => $this->name,
			'id' => $this->id,
			'widgets' => $this->widgets
		];
	}
	
}
