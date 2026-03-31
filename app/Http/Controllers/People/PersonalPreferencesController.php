<?php

namespace App\Http\Controllers\People;

use App\Classes\Settings\SchoolSettings;
use App\Http\Controllers\Controller;
use App\Models\People\Person;
use Illuminate\Http\Request;

class PersonalPreferencesController extends Controller
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
        if ($person->phones->count() > 0) {
            $sendSms = $request->has('send_sms');
            $person->setPreference('communications.send_sms', $sendSms);
            if ($sendSms) {
                $person->setPreference('communications.sms_phone_id', $request->validate(['sms_phone_id' => 'required|exists:phones,id'])['sms_phone_id']);
            }
        } else {
            $person->setPreference('communications.send_sms', false);
        }
        $person->save();

        return redirect()->back()->with('success', __('people.preferences.communications.updated'));
    }

    public function communicationsSubscriptionsUpdate(Request $request, Person $person)
    {
        $data = $request->validate(['subscriptions' => 'nullable|array']);
        $person->schoolMessageSubscriptions()->sync($data['subscriptions'] ?? []);

        return redirect()->back()->with('success', __('people.preferences.communications.updated'));
    }

    public function setPersonalPreference(Request $request)
    {
        $person = auth()->user();
        $input = $request->validate([
            'key' => 'required|string',
            'value' => 'required|string',
        ]);
        $person->setPreference($input['key'], json_decode($input['value'], true));

        return response([], 200);
    }

	public function passkeys(Person $person)
	{
		$breadcrumb =
			[
				__('people.profile.mine') => route('people.show', $person->school_id),
				__('settings.auth.passkeys') => '#',
			];

		return view('people.preferences.passkeys', compact('breadcrumb', 'person'));
	}
}
