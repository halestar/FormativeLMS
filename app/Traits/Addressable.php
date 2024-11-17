<?php

namespace App\Traits;

use App\Models\People\Address;
use App\Models\People\PersonalAddress;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Addressable
{
    public function addresses(): MorphToMany
    {
        return $this->morphToMany(Address::class, 'addressable', 'addressable')
            ->using(PersonalAddress::class)
            ->as('personal')
            ->withPivot(
                [
                    'primary', 'label', 'order'
                ])
            ->orderByPivot('primary', 'desc')
            ->orderByPivot('order', 'asc');
    }

    public function makeAddressPrimary(Address $address):void
    {
        //first, we update th main entry to be the primary and order 0
        $pri = $this->primaryKey;
        PersonalAddress::where('address_id', $address->id)
            ->where('addressable_id', $this->$pri)
            ->where('addressable_type', get_class($this))
            ->update(['primary' => true, 'order' => 0]);
        //updating the primary should make 2 with primary order 0, so adding 1 to ach of the other
        //ones should fix it.
        PersonalAddress::whereNot('address_id', $address->id)
            ->where('addressable_id', $this->$pri)
            ->where('addressable_type', get_class($this))
            ->increment('order', 1, ['primary' => false]);
    }

    public function isSingleAddressable(): bool
    {
        return false;
    }
}
