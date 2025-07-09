<?php

namespace App\Http\Controllers\People;

use App\Casts\IdCard;
use App\Classes\Settings\IdSettings;
use App\Http\Controllers\Controller;
use App\Models\Locations\Campus;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Http\Request;

class IdController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:school.settings', ['except' => ['show']]);
    }
    public function manageGlobal()
    {
        $breadcrumb =
            [
                __('system.menu.school.settings') => route('settings.school'),
                __('people.id.manage') => '#',
            ];
        $settings = IdSettings::instance();
        $idCard = $settings->getGlobalId();
        return view('people.ids.manage-global', compact('breadcrumb', 'idCard'));
    }

    public function updateGlobal(Request $request)
    {
        $settings = IdSettings::instance();
        $idCard = IdCard::hydrate($request->input('school_id'));
        $settings->setGlobalId($idCard);
        return redirect()->back()
            ->with('success-status', __('people.id.updated'));
    }

    public function manageRole(SchoolRoles $role)
    {
        $breadcrumb =
            [
                __('system.menu.school.settings') => route('settings.school'),
                __('people.id.manage') => '#',
            ];
        $settings = IdSettings::instance();
        $idCard = $settings->getRoleId($role);
        return view('people.ids.manage-role', compact('breadcrumb', 'idCard', 'role'));
    }

    public function updateRole(Request $request, SchoolRoles $role)
    {
        $settings = IdSettings::instance();
        $idCard = IdCard::hydrate($request->input('school_id'));
        $settings->setRoleId($role, $idCard);
        return redirect()->back()
            ->with('success-status', __('people.id.updated'));
    }

    public function manageCampus(Campus $campus)
    {
        $breadcrumb =
            [
                __('system.menu.school.settings') => route('settings.school'),
                __('people.id.manage') => '#',
            ];
        $settings = IdSettings::instance();
        $idCard = $settings->getCampusId($campus);
        return view('people.ids.manage-campuses', compact('breadcrumb', 'idCard', 'campus'));
    }

    public function updateCampus(Request $request, Campus $campus)
    {
        $settings = IdSettings::instance();
        $idCard = IdCard::hydrate($request->input('school_id'));
        $settings->setCampusId($campus, $idCard);
        return redirect()->back()
            ->with('success-status', __('people.id.updated'));
    }

    public function manageRoleCampus(SchoolRoles $role, Campus $campus)
    {
        $breadcrumb =
            [
                __('system.menu.school.settings') => route('settings.school'),
                __('people.id.manage') => '#',
            ];
        $settings = IdSettings::instance();
        $idCard = $settings->getRoleCampusId($role, $campus);
        return view('people.ids.manage-both', compact('breadcrumb', 'idCard', 'role', 'campus'));
    }

    public function updateRoleCampus(Request $request, SchoolRoles $role, Campus $campus)
    {
        $settings = IdSettings::instance();
        $idCard = IdCard::hydrate($request->input('school_id'));
        $settings->setRoleCampusId($role, $campus, $idCard);
        return redirect()->back()
            ->with('success-status', __('people.id.updated'));
    }

    public function show()
    {
        $breadcrumb =
            [
                __('people.id.mine') => '#',
            ];
        return view('people.ids.show', compact('breadcrumb'));
    }
}
