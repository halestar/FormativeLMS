<?php

namespace App\Classes\Storage;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class ExportFile implements Arrayable, JsonSerializable
{
	
	public function __construct
	(
		public string $name,
		public string $contents,
		public string $mime,
		public string $extension,
		public int $size
	) {}
	
	public static function hydrate(array $data): ExportFile
	{
		return new ExportFile
		(
			$data['name'],
			$data['contents'],
			$data['mime'],
			$data['extension'],
			$data['size']
		);
	}
	
	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}
	
	public function toArray()
	{
		return
			[
				'name' => $this->name,
				'contents' => $this->contents,
				'mime' => $this->mime,
				'extension' => $this->extension,
				'size' => $this->size
			];
	}
}