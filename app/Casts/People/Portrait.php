<?php

namespace App\Casts\People;

use App\Models\People\Person;
use App\Models\Utilities\WorkFile;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;

class Portrait implements CastsAttributes
{
	public ?string $url = null;
	public ?string $workFileId = null;

	public function __construct(?string $url = null)
	{
		if ($url && Str::isUrl($url))
			$this->url = $url;
		else
			$this->url = asset('images/unk.svg');
		//are we refering to a work file?
		$matches = null;
		$placeholder = 'work_file_uuid';
		$baseUrl = route('settings.work.file', ['work_file' => $placeholder]);
		$routePattern = '/' . str_replace($placeholder, '(.*)', preg_quote($baseUrl, '/')) . '/';

		if (preg_match($routePattern, $this->url, $matches))
		{
			//in this case, the url refers to a work file, so extract the id
			$this->workFileId = $matches[1];
			//does this refer to an actual file?
			if (!$this->getWorkFile())
			{
				//in this case, the file was deleted, so we reset the url
				$this->workFileId = null;
				$this->url = asset('images/unk.svg');
			}
		}
	}

	public function thumbUrl(): string
	{
		if($this->isWorkFile())
			return route('settings.work.file.thumb', ['work_file' => $this->workFileId]);
		//else, return the url itself.
	    return $this->url;
	}

	public function __toString(): string
	{
		return $this->url;
	}

	public function isWorkFile(): bool
	{
		return ($this->workFileId != null);
	}

	public function getWorkFile(): ?WorkFile
	{
		return WorkFile::find($this->workFileId);
	}

    public function remove()
    {
        //if this is a work file, we need to delete it first.
	    if($this->isWorkFile())
		    $this->getWorkFile()->delete();
		//and we reset the url
	    $this->url = null;
    }

	public function useWorkfile(WorkFile $workFile)
	{
		$this->workFileId = $workFile->id;
		$this->url = $workFile->url;
	}

    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return new Portrait($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
		if(!$value instanceof Portrait && !Str::isUrl($value))
			throw new \InvalidArgumentException("The value is not a portrait object or URL");
		//next, is this a portrait object?
	    if($value instanceof Portrait)
			return $value->url;
		//else, we return the URL
	    return $value;
    }
}
