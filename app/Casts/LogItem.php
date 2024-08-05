<?php

namespace App\Casts;

use App\Models\People\Person;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class LogItem implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $logItems =  json_decode($value, true);
        $logs = [];
        if($logItems)
        {
            foreach ($logItems as $log)
                $logs[] = new \App\Models\Utilities\LogItem($log);
        }
        return $logs;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $log = json_decode($attributes[$key], true);
        $logItem = null;
        if($value instanceof \App\Models\Utilities\LogItem)
            $logItem = $value;
        else
            $logItem = new \App\Models\Utilities\LogItem($value);
        if($logItem)
            $log[] = $logItem->toJson();
        return json_encode($log);
    }
}
