<?php

namespace App\Http\Controllers\School;

use App\Classes\Settings\SchoolSettings;
use App\Enums\ClassViewer;
use App\Http\Controllers\Controller;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassMessage;
use App\Notifications\ClassAlert;
use App\Notifications\NewClassMessageNotification;
use Illuminate\Http\Request;
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
		foreach($user->unreadNotifications as $notification)
		{
			if($notification->type == ClassAlert::class &&
				isset($notification->data['session_id']) &&
				$notification->data['session_id'] == $classSession->id)
				$notification->markAsRead();
		}
		//finally, determine the viewing type in this scenario.
		$viewingAs = ClassViewer::determineType($user, $classSession);
		//next, we pull the settings to get the class management connections
		//in this case, we'll force the user to go to the system's class management.
		if($schoolSettings->force_class_management)
			return $schoolSettings->classManagementConnection->manageClass($user, $classSession, $viewingAs);
		else //in this case, we use the user's preferences
			return $user->classManagementSystem()->manageClass($user, $classSession, $viewingAs);

	}
	
	public function classMessages(Request $request)
	{
		$breadcrumb = [trans_choice('subjects.school.message', 2) => "#"];
		$classMessageId = null;
		if($request->has('notification_id'))
		{
			$notification = Auth::user()
			                    ->notifications()
			                    ->where('id', $request->notification_id)
			                    ->first();
			if($notification && $notification->type == NewClassMessageNotification::class)
				$classMessageId = $notification->data['misc']['message_id'];
		}
		$classMsg = null;
		if($classMessageId)
			$classMsg = ClassMessage::find($classMessageId);
		$self = Auth::user();
		$type = "";
		$data = ['size' => 'lg'];
		if($self->isStudent())
		{
			//in case of students,
			$type = "school.student-class-chat";
			$data['selected-session-id'] = null;
			if($classMsg)
				$data['selected-session-id'] = $classMsg->session_id;
			
		}
		elseif($self->isTeacher())
		{
			//in case of teacher
			$type = "school.faculty-class-chat";
			$data['student-id'] = null;
			$data['session-id'] = null;
			if($classMsg)
			{
				$data['session-id'] = $classMsg->session_id;
				$data['student-id'] = $classMsg->student_id;
			}
			
		}
		elseif($self->isParent())
		{
			$type = "school.parent-class-chat";
			$data['student-id'] = null;
			$data['session-id'] = null;
			if($classMsg)
			{
				$data['session-id'] = $classMsg->session_id;
				$data['student-id'] = $classMsg->student_id;
			}
		}
		elseif($self->isEmployee())
		{
			//it must be an admin
			$type = "school.admin-class-chat";
			$data['student-id'] = null;
			$data['session-id'] = null;
			if($classMsg)
			{
				$data['session-id'] = $classMsg->session_id;
				$data['student-id'] = $classMsg->student_id;
			}
		}
		return view('school.class.messages', compact('breadcrumb', 'type', 'data'));
	}
}
