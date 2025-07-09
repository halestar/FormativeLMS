<?php

namespace App\Classes\Synths;

use App\Classes\IdCard\IdCardElement;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class IdCardElementSynth extends Synth
{
    public static $key = "id-card-element";
    public static function match($target)
    {
        return $target instanceof IdCardElement;
    }

    public function dehydrate($target)
    {
        $data = $target->toArray();
        $data['className'] = $target::class;
        return [
            $data
            , []];
    }

    public function hydrate($value): IdCardElement
    {
        return $value['className']::hydrate($value);
    }
}
