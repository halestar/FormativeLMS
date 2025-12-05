<?php

namespace App\Models\SubjectMatter\Components;

use App\Casts\Utilities\CarbonDate;
use App\Casts\Utilities\ColorCast;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class ClassLink extends ClassCommunicationObject
{
    protected static function defaultValue(): array
    {
        return
            [
                'title' => '',
                'url' => '',
            ];
    }

    protected function casts(): array
    {
        return [];
    }

    public function title(): Attribute
    {
        return $this->basicProperty('title');
    }

    public function url(): Attribute
    {
        return $this->basicProperty('url');
    }

}