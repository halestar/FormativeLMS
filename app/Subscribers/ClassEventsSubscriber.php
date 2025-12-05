<?php

namespace App\Subscribers;

use App\Events\Classes\NewClassAlert;
use App\Events\Classes\NewClassMessage;
use App\Events\Classes\NewClassStatusEvent;
use App\Notifications\Classes\ClassAlert;
use App\Notifications\Classes\NewClassMessageNotification;
use App\Notifications\Classes\NewClassStatusNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Notification;

class ClassEventsSubscriber implements ShouldQueue
{

    public function handleNewClassStatusEvent(NewClassStatusEvent $event)
    {
        //we will be sending this update to all the students in this class, as well as all the parents of the students.
        foreach($event->classStatus->classSession->students as $student)
        {
            $student->person->notify(new NewClassStatusNotification($event->classStatus));
            Notification::send($student->person->parents, new NewClassStatusNotification($event->classStatus));
        }
    }

    public function handleNewClassMessage(NewClassMessage $event)
    {
        $recipients = $event->message->student->person->parents
            ->push($event->message->student->person)
            ->concat($event->message->session->teachers)
            ->concat($event->message->student->trackers)
            ->filter(fn($person) => $person->id != $event->message->person_id);
        Notification::send($recipients, new NewClassMessageNotification($event->message));
    }

	public function handleNewClassAlertEvent(NewClassAlert $event)
	{
		foreach($event->alert->classSession->students as $student)
		{
			$student->person->notify(new ClassAlert($event->alert, $event->action));
			Notification::send($student->person->parents, new ClassAlert($event->alert, $event->action));
		}
	}

    public function subscribe(Dispatcher $events)
    {
        $events->listen(NewClassStatusEvent::class, [ClassEventsSubscriber::class, 'handleNewClassStatusEvent']);
        $events->listen(NewClassMessage::class, [ClassEventsSubscriber::class, 'handleNewClassMessage']);
		$events->listen(NewClassAlert::class, [ClassEventsSubscriber::class, 'handleNewClassAlertEvent']);
    }

}