<?php

namespace App\Policies;

use App\Models\People\Person;
use App\Models\Utilities\WorkFile;

class WorkFilePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(Person $person, WorkFile $workFile): bool
    {
        return $workFile->public || $workFile->fileable->canAccessFile($person, $workFile);
    }

}
