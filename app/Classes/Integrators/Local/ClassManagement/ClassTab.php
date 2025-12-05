<?php

namespace App\Classes\Integrators\Local\ClassManagement;

use App\Interfaces\Synthesizable;
use Livewire\Component;

class ClassTab implements Synthesizable
{
	public string $name;
	public array $widgets;
	private string $id;
	private bool $locked = false;
	public bool $containsClassChat = false;
	public bool $containsClassLd = false;
	
	public function __construct(string $name)
	{
		$this->name = $name;
		$this->id = self::generateId($name);
		$this->widgets = [];
	}
	
	private static function generateId(string $name): string
	{
		return uniqid();
	}
	
	public static function hydrate(array $data): static
	{
		$tab = new ClassTab($data['name']);
		$tab->id = $data['id'];
		$tab->locked = $data['locked'];
		$tab->containsClassChat = $data['containsClassChat'];
		$tab->containsClassLd = $data['containsClassLd'];
		$tab->widgets = $data['widgets'];
		return $tab;
	}
	
	public function lock(): void
	{
		$this->locked = true;
	}
	
	public function isLocked(): bool
	{
		return $this->locked;
	}
	
	public function containerHtml(): string
	{
		return '';
	}
	
	public function addWidget(string $widget): void
	{
		$this->widgets[] = $widget;
	}
	
	public function removeWidget(string $widgetId): ClassWidget
	{
		$widgets = [];
		$rWidget = null;
		foreach($this->widgets as $w)
		{
			if($w->getId() == $widgetId)
			{
				$rWidget = $w;
				continue;
			}
			$widgets[] = $w;
		}
		$this->widgets = $widgets;
		return $rWidget;
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
			'locked' => $this->locked,
			'containsClassLd' => $this->containsClassLd,
			'containsClassChat' => $this->containsClassChat,
			'widgets' => $this->widgets
		];
	}
	
}
