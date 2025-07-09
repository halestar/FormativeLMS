<?php

namespace App\Classes\Synths;

use App\Casts\IdCard;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class IdCardSynth extends Synth
{
    public static $key = "id-card";
    public static function match($target)
    {
        return $target instanceof IdCard;
    }

    public function dehydrate($target)
    {
        return [
            $target->toArray()
            , []];
    }

    public function hydrate($value): IdCard
    {
        return IdCard::hydrate($value);
    }
}
