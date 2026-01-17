<?php

namespace App\Classes\Integrators\Local\ClassManagement;

use App\Classes\Integrators\Local\LocalIntegrator;
use App\Enums\IntegratorServiceTypes;
use App\Interfaces\Synthesizable;
use Livewire\Component;

class ClassTab implements Synthesizable
{
	public string $name;
	public ?string $widget;
	private string $id;
	
	public function __construct(string $name)
	{
		$this->name = $name;
		$this->id = uniqid();
		$this->widget = null;
	}
	
	public static function hydrate(array $data): static
	{
		$tab = new ClassTab($data['name']);
		$tab->id = $data['id'];
		$tab->widget = $data['widget']?? null;
		return $tab;
	}

	public function setWidget(string $widget): void
	{
		$this->widget = $widget;
	}
	
	public function removeWidget(string $widget): void
	{
		$this->widget = null;
	}

	public function canRemoveWidget(): bool
	{
		if(!$this->widget) return true;
		//we check if the widget is in the required array
		$classesService = LocalIntegrator::getService(IntegratorServiceTypes::CLASSES);
		return !in_array($this->widget, $classesService->data->required);
	}

	public function canDelete(): bool
	{
		return $this->canRemoveWidget();
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
			'widget' => $this->widget
		];
	}
	
}
