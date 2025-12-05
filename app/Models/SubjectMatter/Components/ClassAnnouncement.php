<?php

namespace App\Models\SubjectMatter\Components;

use App\Casts\Utilities\CarbonDate;
use App\Casts\Utilities\ColorCast;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ClassAnnouncement extends ClassCommunicationObject
{
    protected static function defaultValue(): array
    {
        return
            [
                'title' => '',
                'announcement' => '',
                'color' => '#000',
                'post_from' => date('Y-m-d'),
                'post_to' => date('Y-m-d'),
            ];
    }

    protected function casts(): array
    {
        return
            [
                'color' => ColorCast::class,
                'post_from' => CarbonDate::class,
                'post_to' => CarbonDate::class,
            ];
    }

    public function title(): Attribute
    {
        return $this->basicProperty('title');
    }

    public function announcement(): Attribute
    {
        return $this->basicProperty('announcement');
    }
}