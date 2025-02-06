<?php

namespace App\Classes\Synths;

use App\Classes\NameConstructor;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class NameConstructorSynth extends Synth
{

    public static $key = "nameCreator";
    public static function match($target)
    {
        return $target instanceof NameConstructor;
    }

    public function dehydrate($target)
    {
        return [[
            'tokens' => $target->tokens,
        ], []];
    }

    public function hydrate($value)
    {
        $instance = new NameConstructor();

        $instance->tokens = $value['tokens'];

        return $instance;
    }
}
