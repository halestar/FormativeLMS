<?php

namespace App\Casts\Ai;

use App\Classes\AI\ProviderOption;
use App\Interfaces\Synthesizable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use JsonSerializable;

class ProviderOptions implements CastsAttributes, Arrayable, JsonSerializable, Synthesizable
{
	protected array $options = [];

    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if(!$value || !is_string($value)) return new ProviderOption();
		$json = json_decode($value, true);
		if(!is_array($json)) return new ProviderOption();
		return ProviderOptions::hydrate($json);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if(!$value instanceof ProviderOptions)
	        throw new \InvalidArgumentException("The value " . json_encode($value) . "is not a ProviderOptions object");
		return json_encode($value->toArray());
    }

	public function toArray():  array
	{
		$options = [];
		foreach($this->options as $option)
			$options[] = $option->toArray();
		return $options;
	}

	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}

	public static function hydrate(array $data): static
	{
		$options = [];
		foreach($data as $option)
			$options[$option['field']] = ProviderOption::hydrate($option);
		$obj = new ProviderOptions;
		$obj->options = $options;
		return $obj;
	}

	public function addOption(ProviderOption $option): ProviderOptions
	{
		$this->options[$option->field] = $option;
		return $this;
	}

	public function addOptions(array $options): ProviderOptions
	{
		foreach($options as $option)
		{
			if($option instanceof ProviderOption)
				$this->options[$option->field] = $option;
		}
		return $this;
	}

	public function getOptions(): array
	{
		return $this->options;
	}
}
