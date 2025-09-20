<?php

namespace App\Models\Utilities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class MimeType extends Model
{
	public const FOLDER_HTML = '<i class="fa-solid fa-folder text-warning"></i>';
	public $timestamps = false;
	public $incrementing = false;
	public bool $editing = false;
	protected $table = "mime_types";
	protected $primaryKey = "mime";
	protected $keyType = 'string';
	protected $fillable =
		[
			'mime',
			'extension',
			'icon',
			'is_img',
		];
	
	public static function allowedMimeTypes(): array
	{
		return Mimetype::all()
		               ->pluck('mime')
		               ->toArray();
	}
	
	public static function imageMimeTypes(): array
	{
		return MimeType::where('is_img', true)
		               ->pluck('mime')
		               ->toArray();
	}
	
	public function __toString()
	{
		return $this->mime;
	}
	
	public function extensions(): Attribute
	{
		return Attribute::make
		(
			get: fn($value) => implode([",", " , ", ", "], $value)
		);
	}
	
	protected function casts(): array
	{
		return
			[
				'is_img' => 'boolean',
			];
	}
}
