<?php

namespace App\Models\Utilities;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $primaryKey = 'name';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    public function casts()
    {
        return [
            'value' => 'array',
        ];
    }
}
