<?php

namespace App\Classes\Synths;

use App\Classes\ClassManagement\ClassTab;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class ClassTabSynth extends Synth
{

    public static $key = "classTab";
    public static function match($target)
    {
        return $target instanceof ClassTab;
    }

    public function dehydrate($target)
    {
        return [
            $target->toArray()
        , []];
    }

    public function hydrate($value): ClassTab
    {
        return ClassTab::hydrate($value);
    }
}
