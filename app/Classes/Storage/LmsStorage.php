<?php

namespace App\Classes\Storage;

use App\Classes\Settings\StorageSettings;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

abstract class LmsStorage implements Arrayable, JsonSerializable
{
	public string $instanceProperty;
	
	public function __construct(public string $displayName)
	{
		$this->instanceProperty = uniqid();
	}
	
	abstract public static function prettyName(): string;
	
	public static function hydrate(array $data)
	{
		// the hydrate method REQUIRES that the class name is stored in the data array
		// in the key 'className'
		$className = $data['className'];
		$instance = new $className($data['displayName']);
		$instance->instanceProperty = $data['instanceProperty'];
		$instance->hydrateElements($data);
		return $instance;
	}
	
	abstract protected function hydrateElements(array $data): void;
	
	public static function rules($except = null): array
	{
		$settings = app()->make(StorageSettings::class);
		return
			[
				'displayName' => ['required', 'min:3'],
			];
	}
	
	public function hasAdditionalProperties(): bool
	{
		return count($this->additionalProperties()) > 0;
	}
	
	public function additionalProperties(): array
	{
		return [];
	}
	
	public function setAdditionalProperty(string $key, $value): void {}
	
	public function getAdditionalProperty(string $key, $default = null): mixed { return $default; }
	
	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}
}