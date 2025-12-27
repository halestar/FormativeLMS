<?php

namespace App\Http\Controllers\School;

use App\Classes\Integrators\IntegrationsManager;
use App\Classes\Settings\SchoolSettings;
use App\Enums\IntegratorServiceTypes;
use App\Http\Controllers\Controller;
use App\Models\SubjectMatter\ClassSession;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;

class ClassController extends Controller implements HasMiddleware
{
	
	public static function middleware()
	{
		return ['auth'];
	}
	
	public function show(ClassSession $classSession, SchoolSettings $schoolSettings)
	{
		//authorize that we can be here first.
		Gate::authorize('view', $classSession);
		//mark all notifications for this class.
		$user = Auth::user();
		//next, we get the class management system, which is an integration connection
        $classManager = $classSession->classManager;
        //and we return the integration connection results.
        return $classManager->manageClass($classSession);
	}

	public function settings(ClassSession $classSession = null, IntegrationsManager $manager)
	{
		$self = Auth::user();
		Gate::authorize('has-role', SchoolRoles::$FACULTY);
		if(!$classSession || !$self->teachesClassSession($classSession))
			$classSelected = $self->currentSchoolClasses()->first();
		else
			$classSelected = $classSession->schoolClass;
		$breadcrumb =
		[
			$classSelected->currentSession()->name_with_schedule => route('subjects.school.classes.show', $classSelected->currentSession()),
			__('system.menu.classes.settings') => "#",
		];

		return view('school.classes.settings', compact('classSelected', 'self', 'breadcrumb'));
	}

	public function criteria(ClassSession $classSession = null)
	{
		$self = Auth::user();
		Gate::authorize('has-role', SchoolRoles::$FACULTY);
		if(!$classSession || !$self->teachesClassSession($classSession))
			$classSelected = $self->currentSchoolClasses()->first();
		else
			$classSelected = $classSession->schoolClass;
		$breadcrumb =
			[
				$classSelected->currentSession()->name_with_schedule => route('subjects.school.classes.show', $classSelected->currentSession()),
				__('system.menu.criteria') => "#",
			];
		return view('school.classes.criteria', compact('classSelected', 'self', 'breadcrumb'));
	}
}
