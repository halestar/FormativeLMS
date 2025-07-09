<?php

namespace App\Models\Utilities;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $primaryKey = 'name';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected function casts()
    {
        return [
            'value' => 'array',
        ];
    }
}
