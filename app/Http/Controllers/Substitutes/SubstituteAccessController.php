<?php

namespace App\Http\Controllers\Substitutes;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SubstituteVerificationAccess;
use App\Models\People\Person;
use App\Models\People\Phone;
use App\Models\Substitutes\Substitute;
use App\Models\Substitutes\SubstituteToken;
use App\Notifications\Substitutes\AcceptedRequestAdminNotification;
use App\Notifications\Substitutes\AcceptedRequestSubstituteNotification;
use App\Notifications\Substitutes\AcceptedRequestTeacherNotification;
use App\Notifications\Substitutes\NewSubSignupNotification;
use App\Notifications\Substitutes\NewSubstituteWelcomeNotification;
use App\Notifications\Substitutes\RejectSubsNotification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SubstituteAccessController extends Controller
{
    public static function middleware()
    {
        return
            [
				'guest',
                new Middleware(SubstituteVerificationAccess::class, only: ['verify', 'verifySub']),
            ];
    }

    public function verify(Request $request)
    {
	    $subId = $request->attributes->get('substitute-id');
	    $sub = Substitute::find($subId);
	    if (! $sub)
		    abort(403);
	    $token = (string) $request->input('sub-access-token', '');
        return view('substitutes.access.verify', compact('sub', 'token'));
    }

    public function verifySub(Request $request)
    {
	    $subId = $request->attributes->get('substitute-id');
	    $sub = Substitute::find($subId);
	    if (! $sub)
		    abort(403);
		$person = $sub->person;

        $validated = $request->validate(
		[
            'contact_consent' => ['accepted'],
            'sms_consent' => ['nullable', 'boolean'],
            'phone' => ['required_if:sms_consent,true', 'string', 'max:25'],
            'sub-access-token' => ['required', 'string'],
        ]);

        $smsConsent = (bool) ($validated['sms_consent'] ?? false);
        $sub->email_confirmed = true;
        $sub->sms_confirmed = $smsConsent;
		if(isset($validated['phone']) && $validated['phone'] != '' && $smsConsent)
		{
			$phone = Phone::updateOrCreate(['phone' => Phone::numericPhone($validated['phone'])], ['mobile' => true]);
			$person->phones()->syncWithoutDetaching(
			[
				$phone->id =>
				[
					'primary' => true,
					'label' => __('features.substitutes.phone')
				]
			]);
			$sub->phone_id = $phone->id;
		}
        $sub->account_verified = now();
        $sub->save();
        // next, we remove the token
        $requestToken = SubstituteToken::byToken($validated['sub-access-token']);
        if ($requestToken)
            $requestToken->delete();
		//next we send the welcome notification to the sub
        $person->notify(new NewSubstituteWelcomeNotification($person));
		//and a notification to the admin that a new sub is ready to go
        (new NewSubSignupNotification($sub))->sendToSubscribers();
        return view('substitutes.access.success', compact('sub'));

    }

    public function request()
    {
		$token = request()->input('token');
        // load the token
        $subToken = SubstituteToken::byToken($token);
        if(!$subToken)
            return view('substitutes.access.rejected');
        $sub = $subToken->substitute;
        $subRequest = $subToken->subRequest;
        $classRequests = $subToken->classRequests();
        if ($subRequest->completed)
            return view('substitutes.access.rejected');
        if ($sub->hasConflicts($subRequest))
            return view('substitutes.access.rejected');

        return view('substitutes.access.request', compact('sub', 'subRequest', 'token', 'classRequests'));
    }

    public function accept(Request $request)
    {
        $token = $request->input('token');
        $subToken = SubstituteToken::byToken($token);
        if(!$subToken)
            return view('substitutes.access.rejected');
        DB::beginTransaction();
        $sub = $subToken->substitute;
        $campusRequests = $subToken->campusRequests;
        $subRequest = $subToken->subRequest;
        if($campusRequests->isEmpty())
            return view('substitutes.access.rejected');
        foreach ($campusRequests as $campusRequest)
		{
            $campusRequest->substitute()->associate($sub);
            $campusRequest->responded_on = now();
            $campusRequest->save();
        }
        DB::commit();
        $rejectedSubs = $subRequest->rejectedSubs();
        if ($subRequest->isCompleted())
		{
            $subRequest->completed = true;
            $subRequest->save();
            // and we delete all the tokens for this request
            $subRequest->subTokens()->delete();
        }
        // next, we send all the notifications.
        foreach ($campusRequests as $campusRequest)
            (new AcceptedRequestAdminNotification($campusRequest))->sendToSubscribers();
        // sub notification
        $subToken->substitute->person->notify(new AcceptedRequestSubstituteNotification($subRequest, $sub));
        // teacher notification
        $subRequest->requester->notify(new AcceptedRequestTeacherNotification($subRequest, $sub));
        // rejected subs notifications
        Notification::send($rejectedSubs, new RejectSubsNotification($subRequest));

        return view('substitutes.access.accepted', compact('sub', 'subRequest'));
    }
}
