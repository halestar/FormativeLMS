<?php

namespace App\Subscribers;

use App\Events\Classes\NewClassAlert;
use App\Events\Classes\NewClassMessage;
use App\Events\Classes\NewClassStatusEvent;
use App\Events\Learning\DemonstrationPostedEvent;
use App\Events\Learning\DemonstrationUpdatedEvent;
use App\Notifications\Classes\ClassAlert;
use App\Notifications\Classes\NewClassMessageNotification;
use App\Notifications\Classes\NewClassStatusNotification;
use App\Notifications\Learning\LearningDemonstrationPostedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Notification;

class LearningEventsSubscriber implements ShouldQueue
{

    public function handleLearningDemonstrationPosted(DemonstrationPostedEvent $event)
    {
        //we will be sending this update to all the students that got a Learning Demonstration Opportunity attached to
	    //them and their parents.
        foreach($event->demonstration->opportunities as $opportunity)
        {
	        $opportunity->student->person->notify(new LearningDemonstrationPostedNotification($opportunity));
            Notification::send($opportunity->student->person->parents, new LearningDemonstrationPostedNotification($opportunity));
        }
    }

	public function handleLearningDemonstrationUpdated(DemonstrationUpdatedEvent $event)
	{
		//we will be sending this update to all the students that got a Learning Demonstration Opportunity attached to
		//them and their parents.
		foreach($event->demonstration->opportunities as $opportunity)
		{
			$opportunity->student->person->notify(new LearningDemonstrationPostedNotification($opportunity));
			Notification::send($opportunity->student->person->parents, new LearningDemonstrationPostedNotification($opportunity));
		}
	}

    public function subscribe(Dispatcher $events)
    {
        $events->listen(DemonstrationPostedEvent::class, [LearningEventsSubscriber::class, 'handleLearningDemonstrationPosted']);
	    $events->listen(DemonstrationUpdatedEvent::class, [LearningEventsSubscriber::class, 'handleLearningDemonstrationUpdated']);
    }

}