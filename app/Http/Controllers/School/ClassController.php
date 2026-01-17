<?php

namespace App\Http\Controllers\School;

use App\Classes\Integrators\IntegrationsManager;
use App\Classes\Settings\SchoolSettings;
use App\Enums\ClassViewer;
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
	
	public function show(ClassSession $classSession)
	{
		//authorize that we can be here first.
		Gate::authorize('view', $classSession);
		//get the class manager
		$classManager = $classSession->classManager;
		//Is this class setup yet?
		if(!$classSession->setup_completed)
		{
			//the class is not set up yet, so we need to determine what needs to be done.
			//if we're not the teacher, then we get an error
			if(!$classSession->viewingAs(ClassViewer::FACULTY))
				return view('school.classes.not-setup', compact('classSession'));
			// First, we check if there are any criteria set in this class, if not, we send them to the criteria page.
			if($classSession->classCriteria()->count() == 0)
				return redirect()->route('learning.classes.criteria', ['classSession' => $classSession]);
			//if it's all done, then we pass it to the integration connection to set it up.
			return $classManager->setupClass($classSession);
		}
        //and we return the integration connection results.
        return $classManager->manageClass($classSession);
	}

	public function settings(IntegrationsManager $manager, ClassSession $classSession = null)
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
