<?php

namespace App\Classes\Learning;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class UrlResource implements Arrayable, JsonSerializable
{
    /**
     * Create a new class instance.
     */
    public function __construct(public string $url, public string $title){}
	
	public function toArray()
	{
		return ['url' => $this->url, 'title' => $this->title];
	}
	
	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}
	
	public static function hydrate(array $data): self
	{
		return new self($data['url'], $data['title']);
	}
}
