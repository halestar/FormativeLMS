<?php

namespace App\Classes\Storage;

use App\Classes\Settings\StorageSettings;
use App\Classes\Storage\Work\WorkStorage;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Validation\Rule;
use JsonSerializable;

abstract class LmsStorage implements Arrayable, JsonSerializable
{
    public function __construct(public string $instanceProperty, public string $displayName){}
    abstract public static function prettyName(): string;

    abstract public static function instancePropertyName(): string;

    abstract public static function instancePropertyNameHelp(): string;

    public function hasAdditionalProperties(): bool
    {
        return count($this->additionalProperties()) > 0;
    }

    public function additionalProperties(): array
    {
        return [];
    }
    public function setAdditionalProperty(string $key, $value):void{}
    public function getAdditionalProperty(string $key, $default = null):mixed{ return $default;}

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
	public static function hydrate(array $data)
	{
		// the hydrate method REQUIRES that the class name is stored in the data array
		// in the key 'className'
		$className = $data['className'];
		$instance = new $className($data['instanceProperty'], $data['displayName']);
		$instance->hydrateElements($data);
		return $instance;
	}

    abstract protected function hydrateElements(array $data): void;
	
    public static function rules($except = null): array
    {
        $settings = app()->make(StorageSettings::class);
        return
        [
            'instanceProperty' => ['required', 'min:3', Rule::notIn($settings->instances($except)), 'max:255', 'regex:/^[a-zA-Z0-9_]+$/'],
        ];
    }
}