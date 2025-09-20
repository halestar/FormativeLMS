<?php

namespace App\Models\Utilities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class MimeType extends Model
{
	public $timestamps = false;
	protected $table = "mime_types";
	protected $primaryKey = "mime";
	public $incrementing = false;
	protected $keyType = 'string';
	public bool $editing = false;
	protected $fillable =
		[
			'mime',
			'extension',
			'icon',
			'is_img',
		];
	
	protected function casts(): array
	{
		return
			[
				'is_img' => 'boolean',
			];
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
	
	public static function allowedMimeTypes(): array
	{
		return Mimetype::all()->pluck('mime')->toArray();
	}
	
	public static function imageMimeTypes(): array
	{
		return MimeType::where('is_img', true)->pluck('mime')->toArray();
	}
	
	public const FOLDER_HTML = '<i class="fa-solid fa-folder text-warning"></i>';
}
