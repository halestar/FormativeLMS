<?php

namespace App\Models\Utilities;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DataPayload extends Model
{
    use HasUuids;
    public $timestamps = false;
    protected $table = "data_payloads";
    protected $primaryKey = "id";
    public $incrementing = false;
    protected $keyType = 'string';


    protected function casts(): array
    {
        return
            [
                'payload' => 'array',
            ];
    }
}
