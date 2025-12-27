<?php

namespace App\Http\Controllers\Settings;

use App\Classes\Integrators\IntegrationsManager;
use App\Classes\SessionSettings;
use App\Classes\Settings\AuthSettings;
use App\Classes\Settings\CommunicationSettings;
use App\Classes\Settings\Days;
use App\Classes\Settings\IdSettings;
use App\Classes\Settings\SchoolSettings;
use App\Classes\Settings\StorageSettings;
use App\Enums\IntegratorServiceTypes;
use App\Enums\WorkStoragesInstances;
use App\Http\Controllers\Controller;
use App\Models\Integrations\IntegrationConnection;
use App\Models\People\Person;
use App\Models\Utilities\SchoolMessage;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class SchoolSettingsController extends Controller implements HasMiddleware
{
	public static function middleware()
	{
		return
            [
                'auth',
                new Middleware('can:school', except: ['getSessionSetting', 'setSessionSetting']),
            ];
	}
	
	public function show(IntegrationsManager $integrations)
	{
		$breadcrumb = [__('system.menu.school.settings') => "#"];
		$studentRole = SchoolRoles::findByName(SchoolRoles::$STUDENT);
		$sampleStudent = Person::whereAttachedTo($studentRole)
		                       ->inRandomOrder()
		                       ->first();
		$employeeRole = SchoolRoles::findByName(SchoolRoles::$EMPLOYEE);
		$sampleEmployee = Person::whereAttachedTo($employeeRole)
		                        ->inRandomOrder()
		                        ->first();
		$parentRole = SchoolRoles::findByName(SchoolRoles::$PARENT);
		$sampleParent = Person::whereAttachedTo($parentRole)
		                      ->inRandomOrder()
		                      ->first();
		$workConnections = $integrations->systemConnections(IntegratorServiceTypes::WORK);
		$classManagementServices = $integrations->getAvailableServices(IntegratorServiceTypes::CLASSES);
        $systemMessages = SchoolMessage::system()->get();
		return view('settings.school.show',
			compact('breadcrumb', 'studentRole',
				'sampleStudent', 'sampleParent', 'sampleEmployee', 'employeeRole', 'parentRole', 'workConnections',
				'classManagementServices', 'systemMessages'));
	}
	
	public function update(Request $request, SchoolSettings $settings)
	{
		$data = $request->validate([
			'days' => 'required|array|min:1',
			'start_time' => 'required|date_format:H:i',
			'end_time' => 'required|date_format:H:i',
            'terms_of_service' => 'required|url',
            'privacy_policy' => 'required|url'
		], static::errors());
		//update days
		$days = [];
		foreach($data['days'] as $dayId)
			$days[$dayId] = Days::day($dayId);
		$settings->days = $days;
		$settings->start_time = $data['start_time'];
		$settings->end_time = $data['end_time'];
        $settings->terms_of_service = $data['terms_of_service'];
        $settings->privacy_policy = $data['privacy_policy'];
		$settings->save();
		return redirect()
			->route('settings.school')
			->with('success-status', __('system.settings.update.success'));
	}
	
	private static function errors(): array
	{
		return [
			'days' => __('errors.school.settings.days'),
			'start_time' => __('errors.school.settings.start'),
			'end_time' => __('errors.school.settings.end'),
		];
	}
	
	public function updateClasses(Request $request, SchoolSettings $settings)
	{
		$data = $request->validate([
			'max_msg' => 'required|numeric|min:1',
			'year_messages' => ['required', 'numeric', Rule::in([1, 2])],
            'rubrics_max_points' => 'required|numeric|min:1',
			'force_class_management' => 'required|boolean',
			'class_management_service_id' => 'required|exists:integration_services,id'
		], static::errors());
		//update days
		$settings->max_msg = $data['max_msg'];
		$settings->year_messages = $data['year_messages'];
        $settings->rubrics_max_points = $data['rubrics_max_points'];
		$settings->force_class_management = $data['force_class_management'];
		$settings->class_management_service_id = $data['class_management_service_id'];
		$settings->save();
		return redirect()
			->route('settings.school')
			->with('success-status', __('system.settings.update.success'));
	}
	
	public function nameCreator(SchoolRoles $role)
	{
		$breadcrumb =
			[
				__('system.menu.school.settings') => route('settings.school'),
				__('system.settings.names.title', ['role' => $role->name]) => '#',
			
			];
		return view('settings.school.names', compact('role', 'breadcrumb'));
	}
	
	public function updateId(Request $request, IdSettings $idSettings)
	{
		$data = $request->validate([
			'id_strategy' => ['required', Rule::in(
				[
					IdSettings::ID_STRATEGY_GLOBAL,
					IdSettings::ID_STRATEGY_ROLES,
					IdSettings::ID_STRATEGY_CAMPUSES,
					IdSettings::ID_STRATEGY_BOTH,
				])],
		], static::errors());
		$idSettings->idStrategy = $data['id_strategy'];
		$idSettings->save();
		return redirect()
			->route('settings.school')
			->with('success-status', __('system.settings.update.success'));
	}
	
	public function updateAuth(Request $request, AuthSettings $settings)
	{
		$data = $request->validate([
			'min_password_length' => 'required|numeric|min:8',
		], static::errors());
		$settings->min_password_length = $data['min_password_length'];
		$settings->numbers = $request->has('numbers');
		$settings->upper = $request->has('upper');
		$settings->symbols = $request->has('symbols');
		$settings->save();
		return redirect()
			->route('settings.school')
			->with('success-status', __('system.settings.update.success'));
	}
	
	public function updateStorage(Request $request, StorageSettings $settings)
	{
		$rules = [];
		foreach(WorkStoragesInstances::cases() as $work)
			$rules[$work->value] = "required|uuid|exists:integration_connections,id";
		$data = $request->validate($rules);
		$settings->work_storages = $data;
		$settings->save();
		return redirect()
			->route('settings.school')
			->with('success-status', __('system.settings.update.success'));
	}

    public function updateCommunications(Request $request, CommunicationSettings $settings)
    {
        $data = $request->validate([
            'email_connection_id' => 'required|uuid|exists:integration_connections,id',
            'email_from' => 'required',
            'email_from_address' => 'required|email',
            'send_sms' => 'nullable',
            'sms_connection_id' => 'nullable|exists:integration_connections,id'
        ]);
        //update days
        $settings->email_connection_id = $data['email_connection_id'];
        $settings->email_from = $data['email_from'];
        $settings->email_from_address = $data['email_from_address'];
        $settings->send_sms = $request->has('send_sms');
        $settings->sms_connection_id = $data['sms_connection_id'];
        $settings->save();
        return redirect()
            ->route('settings.school')
            ->with('success-status', __('settings.communications.update.success'));
    }

	public function getSessionSetting(Request $request)
	{
		$key = $request->input('key');
		$s = SessionSettings::instance();
		$default = $request->input('default', []);
		return response()->json($s->get($key, $default), 200);
	}

	public function setSessionSetting(Request $request)
	{
		$s = SessionSettings::instance();
		$key = $request->input('key');
		$value = $request->input('value');
		$s->set($key, $value);
		return response()->json([], 200);
	}
}
