<?php

namespace App\Casts\Utilities\SystemSettings;

use App\Classes\Storage\Document\DocumentStorage;
use App\Classes\Storage\LmsStorage;
use App\Traits\UsesJsonValue;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AsDocumentStorage implements CastsAttributes
{
    use UsesJsonValue;
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $storages = $this->getValue($attributes['value'], $key,[]);
        if(count($storages) == 0)
            return [];
        $workStorages = [];
        foreach($storages as $storage)
            $workStorages[$storage['instanceProperty']] = LmsStorage::hydrate($storage);
        return $workStorages;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if(!is_array($value))
            return ['value' => $attributes['value']];
        $jsonStorages = [];
        foreach($value as $v)
        {
            if(!($v instanceof DocumentStorage))
                return ['value' => $attributes['value']];
            $jsonStorages[] = $v->toArray();
        }
        return $this->updateValue($attributes['value'], $key, $jsonStorages);
    }
}
