<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Days;
use App\Classes\SchoolSettings;
use App\Http\Controllers\Controller;
use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Http\Request;

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
        return view('admin.school.show', compact('schoolSettings', 'breadcrumb', 'studentRole', 'sampleStudent', 'sampleParent', 'sampleEmployee', 'employeeRole', 'parentRole'));
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
        return redirect()->route('school.settings')->with('success-status', __('system.settings.update.success'));
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
}
