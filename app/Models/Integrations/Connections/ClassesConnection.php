<?php

namespace App\Models\Integrations\Connections;

use App\Interfaces\IntegrationConnectionInterface;
use App\Models\Integrations\IntegrationConnection;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\SchoolClass;
use Illuminate\Database\Eloquent\Relations\HasMany;

abstract class ClassesConnection extends IntegrationConnection implements IntegrationConnectionInterface
{
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

	/**
	 * @return bool Returns whether this integration connection has preference for the user (teacher) to set.)
	 */
	abstract public function hasPreferences(): bool;

	/**
	 * If the service has preference the teacher can set, this will return the route to the preferences page.  This page
	 * SHOULD extend the layouts.class-settings view, and put the content in between @section('class_settings_content')
	 * parameters.  It should also pass 2 variables:$breadcrumb with the breadcrumbs, and $classSelected which will be the
	 * selected SchoolClass.
	 * @param SchoolClass $schoolClass This is the School Class model for the properties. Note that the preferences should
	 * apply to all the sessions in the class.
	 * @return string
	 */
	abstract public function preferencesRoute(SchoolClass $schoolClass): string;
}