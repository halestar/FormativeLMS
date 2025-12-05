<?php

namespace App\Http\Controllers\People;

use App\Classes\Settings\SchoolSettings;
use App\Http\Controllers\Controller;
use App\Models\People\Person;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PersonalPreferencesController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return ['auth'];
    }

    public function communications(Person $person, SchoolSettings $schoolSettings)
    {
        $breadcrumb =
            [
                __('people.profile.mine') => route('people.show', $person->school_id),
                __('people.preferences.communications') => '#',
            ];
        $phones = $person->phones;
        return view('people.preferences.communications', compact('breadcrumb', 'person', 'phones', 'schoolSettings'));
    }

    public function communicationsDeliveryUpdate(Request $request, Person $person)
    {
        $person->setPreference('communications.send_email', $request->has('send_email'));
        $person->setPreference('communications.send_push', $request->has('send_push'));
        if($person->phones->count() > 0)
        {
            $sendSms = $request->has('send_sms');
            $person->setPreference('communications.send_sms', $sendSms);
            if($sendSms)
                $person->setPreference('communications.sms_phone_id', $request->validate(['sms_phone_id' => 'required|exists:phones,id'])['sms_phone_id']);
        }
        else
            $person->setPreference('communications.send_sms', false);
        $person->save();
        return redirect()->back()->with('success', __('people.preferences.communications.updated'));
    }

    public function communicationsSubscriptionsUpdate(Request $request, Person $person)
    {
        $data = $request->validate(['subscriptions' => 'nullable|array']);
        $person->schoolMessageSubscriptions()->sync($data['subscriptions']??[]);
        return redirect()->back()->with('success', __('people.preferences.communications.updated'));
    }
}
