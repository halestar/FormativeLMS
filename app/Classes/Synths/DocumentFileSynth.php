<?php

namespace App\Classes\Synths;

use App\Casts\Rubric;
use App\Classes\Storage\Document\DocumentStorage;
use App\Classes\Storage\DocumentFile;
use App\Classes\Storage\LmsStorage;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class DocumentFileSynth extends Synth
{

    public static $key = "document-file";
    public static function match($target)
    {
        return $target instanceof DocumentFile;
    }

    public function dehydrate($target)
    {
        return [
            $target->toArray()
        , []];
    }

    public function hydrate($value): mixed
    {
        return DocumentFile::hydrate($value);
    }
}
