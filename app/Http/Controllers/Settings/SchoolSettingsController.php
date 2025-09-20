<?php

namespace App\Http\Controllers\Settings;

use App\Classes\Days;
use App\Classes\Settings\AuthSettings;
use App\Classes\Settings\IdSettings;
use App\Classes\Settings\SchoolSettings;
use App\Classes\Settings\StorageSettings;
use App\Enums\IntegratorServiceTypes;
use App\Enums\WorkStoragesInstances;
use App\Http\Controllers\Controller;
use App\Models\Integrations\IntegrationConnection;
use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Validation\Rule;

class SchoolSettingsController extends Controller implements HasMiddleware
{
    private static function errors(): array
    {
        return [
            'days' => __('errors.school.settings.days'),
            'start_time' => __('errors.school.settings.start'),
            'end_time' => __('errors.school.settings.end'),
        ];
    }
    public function show()
    {
        $breadcrumb = [ __('system.menu.school.settings') => "#" ];
        $studentRole = SchoolRoles::findByName(SchoolRoles::$STUDENT);
        $sampleStudent = Person::join('model_has_roles', 'model_has_roles.model_id', '=', 'people.id')
            ->where('model_has_roles.role_id', $studentRole->id)
            ->inRandomOrder()
            ->first();
        $employeeRole = SchoolRoles::findByName(SchoolRoles::$EMPLOYEE);
        $sampleEmployee = Person::join('model_has_roles', 'model_has_roles.model_id', '=', 'people.id')
            ->where('model_has_roles.role_id', $employeeRole->id)
            ->inRandomOrder()
            ->first();
        $parentRole = SchoolRoles::findByName(SchoolRoles::$PARENT);
        $sampleParent = Person::join('model_has_roles', 'model_has_roles.model_id', '=', 'people.id')
            ->where('model_has_roles.role_id', $parentRole->id)
            ->inRandomOrder()
            ->first();
		$workConnections = IntegrationConnection::select('integration_connections.*')
			->join('integration_services', 'integration_services.id', '=', 'integration_connections.service_id')
			->whereNull('integration_connections.person_id')
			->where('integration_services.service_type', IntegratorServiceTypes::WORK)
			->get();
        return view('settings.school.show',
	        compact('breadcrumb', 'studentRole',
		        'sampleStudent', 'sampleParent', 'sampleEmployee', 'employeeRole', 'parentRole', 'workConnections'));
    }

    public function update(Request $request, SchoolSettings $settings)
    {
        $data = $request->validate([
            'days' => 'required|array|min:1',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
        ], static::errors());
        //update days
        $days = [];
        foreach($data['days'] as $dayId)
            $days[$dayId] = Days::day($dayId);
        $settings->days = $days;
        $settings->startTime = $data['start_time'];
        $settings->endTime = $data['end_time'];
        $settings->save();
        return redirect()->route('settings.school')->with('success-status', __('system.settings.update.success'));
    }

    public function updateClasses(Request $request, SchoolSettings $settings)
    {
        $data = $request->validate([
            'max_msg' => 'required|numeric|min:1',
            'year_messages' => ['required','numeric', Rule::in([1, 2])],
        ], static::errors());
        //update days
        $settings->max_msg = $data['max_msg'];
        $settings->year_messages = $data['year_messages'];
        $settings->save();
        return redirect()->route('settings.school')->with('success-status', __('system.settings.update.success'));
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
        return redirect()->route('settings.school')->with('success-status', __('system.settings.update.success'));
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
		return redirect()->route('settings.school')->with('success-status', __('system.settings.update.success'));
	}
	
	public function updateStorage(Request $request, StorageSettings $settings)
	{
		$rules = [];
		foreach(WorkStoragesInstances::cases() as $work)
			$rules[$work->value] = "required|uuid|exists:integration_connections,id";
		$data = $request->validate($rules);
		$settings->work_storages = $data;
		$settings->save();
		return redirect()->route('settings.school')->with('success-status', __('system.settings.update.success'));
	}

	public static function middleware()
	{
		return ['auth', 'can:school'];
	}
}
