<?php

namespace App\Utilities;

use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Storage\StorageObject;
use Illuminate\Http\UploadedFile;

class GoogleFileUpload
{
    public static function upload(UploadedFile $file, $path):StorageObject
    {
        $configFile = file_get_contents(config('lms.google.secrets_location'));
        $client = new StorageClient([
            'keyFile' => json_decode($configFile, true)
        ]);
        $bucket = $client->bucket(config('lms.google.storage_bucket'));
        $fsource = fopen($file->getRealPath(), 'r');
        return $bucket->upload($fsource,
            [
                'predefinedAcl' => 'publicRead',
                'name' => $path,
            ]);
    }

}
