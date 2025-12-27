<?php

namespace App\View\Composers;

use App\Classes\SessionSettings;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClassSettingsComposer
{
	public Collection $schoolClasses;
	
	public function __construct(SessionSettings $sessionSettings)
	{
		Gate::authorize('has-role', SchoolRoles::$FACULTY);
		$this->schoolClasses = auth()->user()->currentSchoolClasses();
	}
	
	public function compose(View $view): void
	{
		$view->with('schoolClasses', $this->schoolClasses);
	}
}