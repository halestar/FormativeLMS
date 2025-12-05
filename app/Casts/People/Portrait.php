<?php

namespace App\Casts\People;

use App\Models\People\Person;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;

class Portrait implements CastsAttributes
{
    private function removeOriginalPortrait(array $attributes)
    {
        //we will need the disk where we store the ids.
        $portraitDisk = config('lms.profile_pics_disk');
        //get the file name that we're removing
        $portraitName = basename(parse_url($attributes['portrait_url'], PHP_URL_PATH));
        $portraitThumb = config('lms.profile_thumbs_path') . "/" . $portraitName;
        //remove the image from the disk
        Storage::disk($portraitDisk)->delete($portraitName);
        Storage::disk($portraitDisk)->delete($portraitThumb);
    }

    private function createThumbnail(string $path)
    {
        //we will need the disk where we store the ids.
        $portraitDisk = config('lms.profile_pics_disk');
        $attr = [];
        $manager = new ImageManager(new Driver());
        $thmb = $manager->read(Storage::disk($portraitDisk)->get($path));
        if($thmb)
        {
            $thmb->scaleDown(height: config('lms.thumb_max_height'));
            $thmbPath = config('lms.profile_thumbs_path') . "/" . pathinfo($path,PATHINFO_FILENAME) . ".png";
            Storage::disk($portraitDisk)->put($thmbPath, $thmb->toPng());
            return Storage::disk($portraitDisk)->url($thmbPath);
        }
        return null;
    }


    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return Cache::remember($key . '-' . $model->id, 3600, fn() => $value ?? Person::UKN_IMG);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
		if(isset($attributes['id']) && $attributes['id'])
		{
			//invalidate the cache.
			Cache::forget($key . '-' . $attributes['id']);
			Cache::forget('thumbnail-url-' . $attributes['id']);
			//we will need the disk where we store the ids.
			$portraitDisk = config('lms.profile_pics_disk');
			//if value is null, were removing the image
			if (!$value)
			{
				$this->removeOriginalPortrait($attributes);
				return ['portrait_url' => null, 'thumbnail_url' => null];
			}
			//if the value is null, we're assuming that we're passing a URL
			if (is_string($value) && filter_var($value, FILTER_VALIDATE_URL) !== false)
			{
				//make sure this isn't the image we're currently using.
				if (isset($attributes['portrait_url']) && $attributes['portrait_url'] == $value)
					return ['portrait_url' => $attributes['portrait_url']];
				//if we already have a portrait, remove it.
				if (isset($attributes['portrait_url']) && $attributes['portrait_url'])
					$this->removeOriginalPortrait($attributes);
				//and we copy the image to our own disk
				$ext = pathinfo(parse_url($value, PHP_URL_PATH), PATHINFO_EXTENSION);
				$fname = uniqid() . "." . $ext;
				//ssl?
				if(str_starts_with($value, "https://"))
				{
					$arrContextOptions =
					[
						"ssl" =>
						[
							"verify_peer"=>false,
							"verify_peer_name"=>false,
						],
					];
					$contents = file_get_contents($value, false, stream_context_create($arrContextOptions));
				}
				else
					$contents = file_get_contents($value);
				Storage::disk($portraitDisk)->put($fname, $contents);
				//and we make the thumbnail.
				$thumb = $this->createThumbnail($fname);
				return ['portrait_url' => Storage::disk($portraitDisk)->url($fname), 'thumbnail_url' => $thumb];
			}
			if ($value instanceof UploadedFile)
			{
				//store the file
				$portraitPath = $value->store('', $portraitDisk);
				$portrait = Storage::disk($portraitDisk)->url($portraitPath);
				$thumb = $this->createThumbnail($portraitPath);
				return ['portrait_url' => $portrait, 'thumbnail_url' => $thumb];
			}
			//if there are no valid options, we re-insert the original values
			return ['portrait_url' => $attributes['portrait_url']];
		}
	    return ['portrait_url' => $value];
    }
}
