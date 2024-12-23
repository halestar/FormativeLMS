<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Utilities\SchoolPermission;
use App\Models\Utilities\SchoolRole;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private static function errors(): array
    {
        return [
            'name' => __('errors.roles.name'),
            'permissions' => __('errors.roles.permissions'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Role::class);
        $template =
            [
                'breadcrumb' => [ __('settings.settings') => '#', __('settings.roles') => '#'],
                'title' => __('settings.roles'),
                'buttons' => [],
            ];
        if(Auth::user()->can('settings.roles.create'))
        {
            $template['buttons'] =
                [
                    'create' =>
                        [
                            'url' => route('settings.roles.create'),
                            'classes' => 'btn btn-primary',
                            'text' => __('settings.role.new'),
                        ],
                ];
        }
        return view('settings.roles.index', compact('template'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Role::class);
        $breadcrumb =
            [
                __('settings.settings') => '#',
                __('settings.roles') => route('settings.roles.index'),
                __('settings.role.new') => '#'
            ];
        return view('settings.roles.create', compact('breadcrumb'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Role::class);
        $data = $request->validate([
            'name' => 'required|unique:roles,name|max:255',
            'permissions' => 'required|array|min:1',
        ], static::errors());
        $role = new SchoolRoles();
        $role->name = $data['name'];
        $role->save();
        $role->syncPermissions($request->input('permissions', []));
        return redirect()->route('settings.roles.index')->with('success-status', __('settings.role.created'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SchoolRoles $role)
    {
        Gate::authorize('update', $role);
        $breadcrumb =
            [
                __('settings.settings') => '#',
                __('settings.roles') => route('settings.roles.index'),
                __('settings.role.edit') => '#'
            ];
        return view('settings.roles.edit', compact('role', 'breadcrumb'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SchoolRoles $role)
    {
        Gate::authorize('update', $role);
        $data = $request->validate([
            'name' => ['required', 'max:255', Rule::unique('roles')->ignore($role)],
            'permissions' => 'required|array|min:1',
        ], static::errors());
        if(!$role->base_role)
            $role->name = $data['name'];
        $role->syncPermissions($request->input('permissions', []));
        $role->save();
        return redirect()->route('settings.roles.index')->with('success-status', __('settings.role.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchoolRoles $role)
    {
        Gate::authorize('delete', $role);
        if(!$role->base_role)
            $role->delete();
        return redirect()->route('settings.roles.index')->with('success-status', __('settings.role.deleted'));
    }
}
