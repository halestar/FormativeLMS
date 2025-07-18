<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Days;
use App\Classes\Settings\IdSettings;
use App\Classes\Settings\SchoolSettings;
use App\Http\Controllers\Controller;
use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:school']);
    }

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
        $schoolSettings = SchoolSettings::instance();
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
        $idSettings = IdSettings::instance();
        return view('admin.school.show', compact('schoolSettings', 'breadcrumb', 'studentRole', 'sampleStudent', 'sampleParent', 'sampleEmployee', 'employeeRole', 'parentRole', 'idSettings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'days' => 'required|array|min:1',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
        ], static::errors());
        $settings = SchoolSettings::instance();
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

    public function updateClasses(Request $request)
    {
        $data = $request->validate([
            'max_msg' => 'required|numeric|min:1',
            'year_messages' => ['required','numeric', Rule::in([1, 2])],
        ], static::errors());
        $settings = SchoolSettings::instance();
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
                __('system.menu.school.settings') => route('school.settings'),
                __('system.settings.names.title', ['role' => $role->name]) => '#',

            ];
        return view('admin.school.names', compact('role', 'breadcrumb'));
    }

    public function updateId(Request $request)
    {
        $idSettings = IdSettings::instance();
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
}
