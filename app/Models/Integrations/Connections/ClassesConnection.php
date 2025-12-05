<?php

namespace App\Models\Integrations\Connections;

use App\Enums\ClassViewer;
use App\Interfaces\IntegrationConnectionInterface;
use App\Models\Integrations\IntegrationConnection;
use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;
use Illuminate\Database\Eloquent\Relations\HasMany;

abstract class ClassesConnection extends IntegrationConnection implements IntegrationConnectionInterface
{
	final public static function getInstanceDefault(): array
	{
		return [];
	}

    public function classSessions(): HasMany
    {
        return $this->hasMany(ClassSession::class, 'class_management_id');
    }

    /**
     * This will either display or redirect the user (Auth()->user()) to the class management page for the given class ($classSession)
     * and it will establish what viewing role the $person should have ($viewRole)
     * @param ClassSession $classSession The class session to show the management
     * @return mixed This is called from the controller, so it can return a view, or a redirect.
     */
	abstract public function manageClass(ClassSession $classSession): mixed;
}