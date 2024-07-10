<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Utilities\SchoolPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{

    private static function errors(): array
    {
        return [
            'name' => __('errors.permissions.name'),
            'category_id' => __('errors.permissions.category'),
            'description' => __('errors.permissions.description'),
        ];
    }

    public function index()
    {
        Gate::authorize('has-permission', 'settings.permissions.view');
        $template =
            [
                'breadcrumb' => [ __('settings.settings') => '#', __('settings.permissions') => '#'],
                'title' => __('settings.permissions'),
                'buttons' =>[],
            ];
        if(Auth::user()->can('settings.permissions.create'))
        {
            $template['buttons'] =
                [
                    'create' =>
                        [
                            'url' => route('settings.permissions.create'),
                            'classes' => 'btn btn-primary',
                            'text' => __('settings.permission.new'),
                        ],
                ];
        }
        return view('settings.permissions.index', compact('template'));
    }

    public function create()
    {
        Gate::authorize('has-permission', 'settings.permissions.create');
        $breadcrumb =
            [
                __('settings.settings') => '#',
                __('settings.permissions') => route('settings.permissions.index'),
                __('settings.permission.new') => '#'
            ];
        return view('settings.permissions.create', compact('breadcrumb'));
    }

    public function store(Request $request)
    {
        Gate::authorize('has-permission', 'settings.permissions.create');
        $data = $request->validate([
            'category_id' => 'required|numeric|exists:permission_categories,id',
            'name' => 'required|unique:permissions,name|max:255',
            'description' => 'required|min:25|max:255',
            'roles' => 'nullable|array',
        ], static::errors());
        $permission = new SchoolPermission();
        $permission->fill($data + ['guard_name' => 'web']);
        $permission->save();
        $permission->syncRoles($request->input('roles', []));
        return redirect()->route('settings.permissions.index')->with('success-status', __('settings.permission.created'));
    }

    public function edit(Request $request, SchoolPermission $permission)
    {
        Gate::authorize('has-permission', 'settings.permissions.edit');
        $breadcrumb =
            [
                __('settings.settings') => '#',
                __('settings.permissions') => route('settings.permissions.index'),
                __('settings.permission.edit') => '#'
            ];
        return view('settings.permissions.edit', compact('permission', 'breadcrumb'));
    }

    public function update(Request $request, SchoolPermission $permission)
    {
        Gate::authorize('has-permission', 'settings.permissions.edit');
        $data = $request->validate([
            'category_id' => 'required|numeric|exists:permission_categories,id',
            'name' => ['required', 'max:255', Rule::unique('permissions')->ignore($permission)],
            'description' => 'required|min:25|max:255',
            'roles' => 'nullable|array',
        ], static::errors());
        $permission->fill($data);
        $permission->syncRoles($request->input('roles', []));
        $permission->save();
        return redirect()->route('settings.permissions.index')->with('success-status', __('settings.permission.updated'));
    }

    public function destroy(SchoolPermission $permission)
    {
        Gate::authorize('has-permission', 'settings.permissions.delete');
        $permission->delete();
        return redirect()->route('settings.permissions.index')->with('success-status', __('settings.permission.deleted'));
    }
}
