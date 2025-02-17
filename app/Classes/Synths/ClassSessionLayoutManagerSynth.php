<?php

namespace App\Classes\Synths;

use App\Classes\ClassManagement\ClassSessionLayoutManager;
use App\Models\SubjectMatter\ClassSession;
use Illuminate\Support\Facades\Log;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class ClassSessionLayoutManagerSynth extends Synth
{

    public static $key = "classSessionLayoutManager";
    public static function match($target)
    {
        return $target instanceof ClassSessionLayoutManager;
    }

    public function dehydrate($target)
    {
        return [[
            'layout' => json_encode($target->layout),
            'owner' => $target->owner->id,
        ], []];
    }

    public function hydrate($value)
    {
        Log::debug('Hydrating ClassSessionLayoutManager with ' . print_r($value, true));
        $layout = $value['layout'];
        $owner = ClassSession::find($value['owner']);
        return new ClassSessionLayoutManager($layout, $owner);
    }
}
