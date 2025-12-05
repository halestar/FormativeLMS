<?php

namespace App\Casts\People;
use App\Models\People\Person;

class PreferenceManager
{
	private array $data;
	private Person $owner;
	
	public function __construct(Person $owner, mixed $data = null)
	{
		$this->owner = $owner;
		$data = json_decode($data, true);
		if($data == null || !is_array($data) || count($data) == 0)
			$data = config('lms.prefs_default', []);
		$this->data = $data;
	}
	
	public function get(string $key, mixed $default = null): mixed
	{
		return $this->data[$key] ?? $default;
	}
	
	public function set(string $key, $value): void
	{
		$this->data[$key] = $value;
		$this->owner->prefs = $this;
	}
	
	public function getData(): array
	{
		return $this->data;
	}
}
