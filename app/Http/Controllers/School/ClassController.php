<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SubjectMatter\ClassSession;
use App\Notifications\ClassAlert;
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
}
