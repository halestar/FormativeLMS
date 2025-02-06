<?php

namespace App\Classes\ClassManagement;

class ClassTab
{
    public string $name;
    private string $id;
    public array $widgets;
    private bool $locked = false;

    private static function generateId(string $name): string
    {
        return str_replace([' ', "'", '"'], ['-', '', ''], strtolower($name));
    }

    public function lock(): void
    {
        $this->locked = true;
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->id = self::generateId($name);
        $this->widgets = [];
    }

    public function containerHtml():string
    {
        return '';
    }

    public function addWidget(ClassWidget $widget): void
    {
        $widget->setOrder(count($this->widgets));
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

    public function toArray(): array
    {
        $arr = [
            'name' => $this->name,
            'id' => $this->id,
            'locked' => $this->locked,
            'widgets' => [],
        ];
        foreach($this->widgets as $widget)
            $arr['widgets'][] = $widget->toArray();
        return $arr;
    }

    public static function hydrate(array $data): ClassTab
    {
        $tab = new ClassTab($data['name']);
        $tab->id = $data['id'];
        $tab->locked = $data['locked'];
        $tab->widgets = [];
        foreach($data['widgets'] as $widget)
            $tab->widgets[] = $widget['className']::hydrate($widget);
        return $tab;
    }

    public function getId(): string
    {
        return $this->id;
    }

}
