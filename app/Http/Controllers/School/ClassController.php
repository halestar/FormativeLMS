<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassMessage;
use App\Notifications\ClassAlert;
use App\Notifications\NewClassMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ClassController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(ClassSession $classSession)
    {
        Gate::authorize('view', $classSession);
        $breadcrumb = [ $classSession->name => "#" ];
        //mark all notifications for this class.
        $user = Auth::user();
        foreach($user->unreadNotifications as $notification)
        {
            if($notification->type == ClassAlert::class &&
                isset($notification->data['session_id']) &&
                $notification->data['session_id'] == $classSession->id)
                $notification->markAsRead();
        }
        return view('school.class.show', compact('classSession', 'breadcrumb'));
    }

    public function classMessages(Request $request)
    {
        $breadcrumb = [ trans_choice('subjects.school.message', 2) => "#" ];
        $classMessageId = null;
        if($request->has('notification_id'))
        {
            $notification = Auth::user()->notifications()->where('id', $request->notification_id)->first();
            if($notification && $notification->type == NewClassMessageNotification::class)
                $classMessageId = $notification->data['misc']['message_id'];
        }
        $classMsg = null;
        if($classMessageId)
            $classMsg = ClassMessage::find($classMessageId);
        $self = Auth::user();
        $type = "";
        $data = [ 'size' => 'lg'];
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
            $type = "school.admin-class-chat";$data['student-id'] = null;
            $data['session-id'] = null;
            if($classMsg)
            {
                $data['session-id'] = $classMsg->session_id;
                $data['student-id'] = $classMsg->student_id;
            }
        }
        return view('school.class.messages', compact( 'breadcrumb', 'type', 'data'));
    }
}
