<?php

namespace App\Classes\AI;

use App\Enums\AiSchemaType;

class AiSchema
{
    protected AiSchemaType $type;
    protected ?AiSchema $items;
    protected ?array $properties;
    protected ?array $required;
    protected string $description;
    protected string $name;

    public function __construct(AiSchemaType $type, string $name, string $description, AiSchema $items = null, array $properties = null, array $required = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->items = $items;
        $this->properties = $properties;
        $this->required = $required;
        $this->description = $description;
    }

    public function getType(): AiSchemaType
    {
        return $this->type;
    }

    public function getItems(): ?AiSchema
    {
        return $this->items;
    }

    public function getProperties(): ?array
    {
        return $this->properties;
    }

    public function getRequired(): ?array
    {
        return $this->required;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        $arr = [];
        $arr['type'] = $this->type;
        if($this->items)
            $arr['items'] = $this->items->toArray();
        if($this->properties)
        {
            $props = [];
            foreach($this->properties as $val)
                $props[] = $val->toArray();
            $arr['properties'] = $props;
        }
        if($this->required)
            $arr['required'] = $this->required;
        $arr['description'] = $this->description;
        return $arr;
    }

	public function isNullable(): bool
	{
		return ($this->required == null) || !is_array($this->required) || count($this->required) == 0;
	}
}