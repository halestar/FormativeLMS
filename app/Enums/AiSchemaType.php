<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum AiSchemaType
{
    use EnumToArray;
    case NUMBER;
    case STRING;
    case BOOLEAN;
    case OBJECT;
    case ARRAY;
    case NULL;
}
