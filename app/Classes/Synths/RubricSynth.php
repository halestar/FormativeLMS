<?php

namespace App\Classes\Synths;

use App\Casts\Rubric;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class RubricSynth extends Synth
{

    public static $key = "rubric";
    public static function match($target)
    {
        return $target instanceof Rubric;
    }

    public function dehydrate($target)
    {
        return [
            $target->toArray()
        , []];
    }

    public function hydrate($value): Rubric
    {
        return Rubric::hydrate($value);
    }
}
