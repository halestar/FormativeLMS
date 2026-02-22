<?php

namespace App\Classes\AI;

use App\Enums\BasicDataInput;
use App\Interfaces\Synthesizable;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class ProviderOption implements Arrayable, JsonSerializable, Synthesizable
{
	public string $field = "";
	public string $title = "";
	public string $description = "";
	public BasicDataInput $type = BasicDataInput::TEXT;
	public mixed $value = null;
	public array $choices = [];


	public function toArray(): array
	{
		return
			[
				'field' => $this->field,
				'title' => $this->title,
				'description' => $this->description,
				'type' => $this->type->value,
				'value' => $this->value,
				'choices' => $this->choices,
			];
	}

	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}

	public static function hydrate($data): static
	{
		$obj = new ProviderOption;
		$obj->field = $data['field'];
		$obj->title = $data['title'] ?? $data['field'];
		$obj->description = $data['description'];
		$obj->type = ($data['type'] instanceof BasicDataInput)? $data['type'] : BasicDataInput::from($data['type']);
		$obj->value = $data['value'] ?? null;
		$obj->choices = $data['choices'] ?? [];
		return $obj;
	}

	public static function create($data): ProviderOption
	{
		return ProviderOption::hydrate($data);
	}
}
