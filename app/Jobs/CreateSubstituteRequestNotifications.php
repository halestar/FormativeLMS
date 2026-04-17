<?php

namespace App\Jobs;

use App\Models\Substitutes\SubstituteRequest;
use App\Notifications\Substitutes\NewRequestAdminNotification;
use App\Notifications\Substitutes\NewRequestSubNotification;
use App\Notifications\Substitutes\NewSubRequestTeacherNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateSubstituteRequestNotifications implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public SubstituteRequest $subRequest){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
	    //first, we determine the list of subs that need to be contacted.
	    //We do this for each campus request in the subrequst
	    $subs = [];
	    foreach($this->subRequest->campusRequests as $campusRequest)
	    {
		    foreach($campusRequest->availableSubs() as $person)
		    {
				$sub = $person->substituteProfile;
			    //check if this sub is already subbing a class during these times.
			    if($sub->hasConflicts($this->subRequest))
				    continue;
			    if(!isset($subs[$sub->person_id]))
			    {
				    //we make the sub array, which contains the sub itself and the token generated.
				    $token = $sub->createRequestToken($this->subRequest);
				    $subs[$sub->person_id] =
					    [
						    'sub' => $person,
						    'token' => $token,
						    'link' => route('subs.request', ['token' => $token->plainTextToken]),
					    ];
			    }
			    //finally, we attach the campus request to the token
			    $subs[$sub->person_id]['token']->campusRequests()->attach($campusRequest);
		    }
	    }
	    //we now have a list of subs, so we need to notify them.
	    foreach($subs as $sub)
		    $sub['sub']->notify(new NewRequestSubNotification($this->subRequest, $sub['link']));
	    //mail to the admin group.
	    (new NewRequestAdminNotification($this->subRequest, collect($subs)->pluck('sub')))->sendToSubscribers();
	    //finally, we notify the teacher.
		$this->subRequest->requester->notify(new NewSubRequestTeacherNotification($this->subRequest));
    }
}
