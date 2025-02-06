<?php

namespace App\Classes;

use App\Models\Utilities\SystemSetting;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SchoolSettings extends SystemSetting
{
    protected $table = "system_settings";
    private static string $key = "school";
    private static ?SchoolSettings $instance = null;
    public $timestamps = false;

    public static function instance(): SchoolSettings
    {
        if(self::$instance === null)
            self::$instance = SchoolSettings::find(self::$key);
        if(self::$instance === null)
        {
            //in this case, there's no data, so make an empty space
            $setting = new SchoolSettings();
            $setting->name = self::$key;
            $setting->value = [];
            $setting->save();
            self::$instance = $setting;
        }
        return self::$instance;
    }

    private function updateValue(string $values, string $key, mixed $value)
    {
        $data = json_decode($values, true);
        $data[$key] = $value;
        return ['value' => json_encode($data)];
    }

    private function getValue(string $values, string $key, mixed $default = null)
    {
        $data = json_decode($values, true);
        return $data[$key] ?? $default;
    }

    public function days(): Attribute
    {
        return Attribute::make
        (
            get: fn(mixed $value, array $attributes) =>
                $this->getValue($attributes['value'], 'days', Days::weekdaysOptions()),
            set: fn(mixed $value, array $attributes) =>
                $this->updateValue($attributes['value'], 'days', $value),
        );
    }

    public function startTime(): Attribute
    {
        return Attribute::make
        (
            get: fn(mixed $value, array $attributes) =>
            $this->getValue($attributes['value'], 'start', "08:00"),
            set: fn(mixed $value, array $attributes) =>
                $this->updateValue($attributes['value'], 'start', $value),
        );
    }

    public function endTime(): Attribute
    {
        return Attribute::make
        (
            get: fn(mixed $value, array $attributes) =>
                $this->getValue($attributes['value'], 'end', "16:00"),
            set: fn(mixed $value, array $attributes) =>
                $this->updateValue($attributes['value'], 'end', $value),
        );
    }

    public function studentName(): Attribute
    {
        return Attribute::make
        (
            get: function(mixed $value, array $attributes): NameConstructor
            {
                $tokens = $this->getValue($attributes['value'], 'studentName', []);
                $nameTokens = [];
                foreach($tokens as $token)
                    $nameTokens[] = new NameToken($token['type'], $token);
                return new NameConstructor($nameTokens);
            },
            set: function(NameConstructor $value, array $attributes)
            {
                return $this->updateValue($attributes['value'], 'studentName', $value->tokens);
            },
        );
    }

    public function employeeName(): Attribute
    {
        return Attribute::make
        (
            get: function(mixed $value, array $attributes): NameConstructor
            {
                $tokens = $this->getValue($attributes['value'], 'employeeName', []);
                $nameTokens = [];
                foreach($tokens as $token)
                    $nameTokens[] = new NameToken($token['type'], $token);
                return new NameConstructor($nameTokens);
            },
            set: function(NameConstructor $value, array $attributes)
            {
                return $this->updateValue($attributes['value'], 'employeeName', $value->tokens);
            },
        );
    }

    public function parentName(): Attribute
    {
        return Attribute::make
        (
            get: function(mixed $value, array $attributes): NameConstructor
            {
                $tokens = $this->getValue($attributes['value'], 'parentName', []);
                $nameTokens = [];
                foreach($tokens as $token)
                    $nameTokens[] = new NameToken($token['type'], $token);
                return new NameConstructor($nameTokens);
            },
            set: function(NameConstructor $value, array $attributes)
            {
                return $this->updateValue($attributes['value'], 'parentName', $value->tokens);
            },
        );
    }
}
