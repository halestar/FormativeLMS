<?php

namespace App\Classes;

use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class RoleFieldSynth extends Synth
{

    public static $key = "roleField";
    public static function match($target)
    {
        return $target instanceof RoleField;
    }

    public function dehydrate($target)
    {
        return [[
            'fieldName' => $target->fieldName,
            'fieldType' => $target->fieldType,
            'fieldHelp' => $target->fieldHelp,
            'fieldPlaceholder' => $target->fieldPlaceholder,
            'fieldOptions' => $target->fieldOptions,
        ], []];
    }

    public function hydrate($value)
    {
        $instance = new RoleField();

        $instance->fieldName = $value['fieldName'];
        $instance->fieldType = $value['fieldType'];
        $instance->fieldHelp = $value['fieldHelp'];
        $instance->fieldPlaceholder = $value['fieldPlaceholder'];
        $instance->fieldOptions = $value['fieldOptions'];

        return $instance;
    }
}
