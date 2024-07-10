<?php

namespace App\Models\CMS;

use App\Google\GoogleCloudStorage;
use App\Utilities\GoogleFileUpload;
use Google\Cloud\Storage\ObjectIterator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Google\Cloud\Storage\StorageObject;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BlogPost extends Model
{
    public static string $objPrefix = "posts";
    protected $fillable = ['title', 'slug', 'posted_by', 'body', 'published'];
    protected $casts =
        [
            'published' => 'boolean',
            'created_at' => 'datetime: m/d/Y h:i A',
        ];
    public function scopePublished(Builder $query): void
    {
        $query->where('published', true);
    }

    public static function files(): array
    {
        return Storage::disk('cms')->files(static::$objPrefix);
    }

    public static function uploadFile(UploadedFile $file): string
    {
        $path = $file->storeAs(static::$objPrefix, $file->getClientOriginalName(), ['disk' => "cms"]);
        return Storage::disk('cms')->url($path);
    }
}
