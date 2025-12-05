<?php

namespace App\Http\Controllers\School;

use App\Classes\Settings\SchoolSettings;
use App\Http\Controllers\Controller;
use App\Models\SubjectMatter\ClassSession;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
}
