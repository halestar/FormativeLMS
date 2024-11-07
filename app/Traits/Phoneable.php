<?php

namespace App\Traits;

use App\Models\People\PersonalPhone;
use App\Models\People\Phone;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Phoneable
{
    public function phones(): MorphToMany
    {
        return $this->morphToMany(Phone::class, 'phoneable', 'phoneables')
            ->using(PersonalPhone::class)
            ->as('personal')
            ->withPivot(
                [
                    'primary', 'label', 'order'
                ])
            ->orderByPivot('primary', 'desc')
            ->orderByPivot('order', 'asc');
    }

    public function makePrimary(Phone $phone):void
    {
        //first, we update th main entry to be the primary and order 0
        $pri = $this->primaryKey;
        PersonalPhone::where('phone_id', $phone->id)
            ->where('phoneable_id', $this->$pri)
            ->where('phoneable_type', get_class($this))
            ->update(['primary' => true, 'order' => 0]);
        //updating the primary should make 2 with primary order 0, so adding 1 to ach of the other
        //ones should fix it.
        PersonalPhone::whereNot('phone_id', $phone->id)
            ->where('phoneable_id', $this->$pri)
            ->where('phoneable_type', get_class($this))
            ->increment('order', 1, ['primary' => false]);
    }
}
