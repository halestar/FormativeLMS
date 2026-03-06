<?php

namespace App\Http\Controllers\Substitutes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;

class SubstituteAccessController extends Controller
{
    public static function middleware()
    {
        return
            [
                new Middleware(SubstituteVerificationAccess::class, only: ['verify', 'verifySub']),
            ];
    }

    public function verify(Request $request)
    {
        $subId = $request->attributes->get('substitute-id');
        $sub = Substitute::find($subId);
        if (! $sub) {
            abort(403);
        }
        $token = (string) $request->input('sub-access-token', '');

        return view('substitutes.access.verify', compact('sub', 'token'));
    }

    public function verifySub(Request $request)
    {
        $subId = $request->attributes->get('substitute-id');
        $sub = Substitute::find($subId);
        if (! $sub) {
            abort(403);
        }

        $validated = $request->validate([
            'contact_consent' => ['accepted'],
            'sms_consent' => ['nullable', 'boolean'],
            'phone' => ['required_if:sms_consent,true', 'string', 'max:25'],
            'sub-access-token' => ['required', 'string'],
        ]);

        $smsConsent = (bool) ($validated['sms_consent'] ?? false);
        $sub->email_confirmed = true;
        $sub->sms_confirmed = $smsConsent;
        $sub->phone = $smsConsent ? preg_replace('/[^0-9]/', '', $validated['phone']) : null;
        $sub->account_verified = now();
        $sub->save();
        // next, we remove the token
        $requestToken = SubRequestToken::byToken($validated['sub-access-token']);
        if ($requestToken) {
            $requestToken->delete();
        }
        Mail::to($sub)->send(new NewSubstituteWelcomeEmail($sub));
        NotificationGroup::notify(new NewSubSignupNotification($sub));
        // if the user agreed to text messages, send them one.
        if ($sub->sms_confirmed) {
            Notification::send($sub, new NewSubWelcomeSms($sub));
        }

        return view('substitutes.access.success', compact('sub'));

    }

    public function request(string $token)
    {
        // load the token
        $subToken = SubRequestToken::byToken($token);
        if (! $subToken) {
            return view('substitutes.access.rejected');
        }
        $sub = $subToken->substitute;
        $subRequest = $subToken->subRequest;
        $classRequests = $subToken->classRequests();
        if ($subRequest->completed) {
            return view('substitutes.access.rejected');
        }
        if ($sub->hasConflicts($subRequest)) {
            return view('substitutes.access.rejected');
        }

        return view('substitutes.access.request', compact('sub', 'subRequest', 'token', 'classRequests'));
    }

    public function accept(Request $request)
    {
        $token = $request->input('token');
        $subToken = SubRequestToken::byToken($token);
        if (! $subToken) {
            return view('substitutes.access.rejected');
        }
        DB::beginTransaction();
        $sub = $subToken->substitute;
        $campusRequests = $subToken->campusRequests;
        $subRequest = $subToken->subRequest;
        if ($campusRequests->isEmpty()) {
            return view('substitutes.access.rejected');
        }
        foreach ($campusRequests as $campusRequest) {
            $campusRequest->substitute()->associate($sub);
            $campusRequest->responded_on = now();
            $campusRequest->save();
        }
        DB::commit();
        $rejectedSubs = $subRequest->rejectedSubs();
        if ($subRequest->isCompleted()) {
            $subRequest->completed = true;
            $subRequest->save();
            // and we delete all the tokens for this request
            $subRequest->subTokens()->delete();
        }
        // next, we send all the notifications.
        foreach ($campusRequests as $campusRequest) {
            NotificationGroup::notify(new AcceptedRequestAdminNotification($campusRequest));
        }
        // sub notification
        Mail::to($subToken->substitute)->send(new AcceptedSubRequestSubMessage($subRequest, $sub));
        // teacher notification
        Mail::to($subRequest->requester)->send(new AcceptedSubRequestTeacherMessage($subRequest, $sub));
        // rejected subs notifications
        Notification::send($rejectedSubs, new RejectSubsNotification($subRequest));

        return view('substitutes.access.accepted', compact('sub', 'subRequest'));
    }
}
