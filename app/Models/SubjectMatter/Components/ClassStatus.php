<?php

namespace App\Models\SubjectMatter\Components;

use App\Casts\Utilities\CarbonDate;
use App\Casts\Utilities\ColorCast;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ClassStatus extends ClassCommunicationObject
{
    public static function defaultValue(): array
    {
        return
            [
                "announcement" => '',
                "color" => "#fff",
                "expiry" => date('Y-m-d'),
            ];
    }

    protected function casts(): array
    {
        return
            [
                'color' => ColorCast::class,
                'expiry' => CarbonDate::class,
            ];
    }


    public function announcement(): Attribute
    {
        return $this->basicProperty('announcement');
    }

	public function hasAnnouncement(): bool
	{
		return $this->announcement !== '' &&
            !$this->expiry->isPast();
	}
}
