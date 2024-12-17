<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait EnumToArray
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toArray(): array
    {
        return array_combine(self::values(), self::names());
    }

    public static function fromValue(string $value): self
    {
        foreach (self::cases() as $case) {
            Log::debug("for " . $case->name . " comparing " . $value . " to " . $case->value);
            if( $value == $case->value ){
                return $case;
            }
        }
        throw new \ValueError("$value is not a valid backing value for enum " . self::class );
    }
}
