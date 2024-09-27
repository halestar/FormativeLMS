<?php

namespace App\Http\Controllers\People;

use App\Enums\PolicyType;
use App\Http\Controllers\Controller;
use App\Models\People\ViewPolicies\ViewableField;
use App\Models\People\ViewPolicies\ViewPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ViewPolicyController extends Controller
{
    private function errors()
    {
        return
        [
            'name' => __('people.policies.view.viewable_policy.name.error'),
            'base_role' => __('people.policies.view.viewable_policy.base_role.error'),
            'role_id' => __('people.policies.view.viewable_policy.role.error'),
        ];
    }

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        Gate::authorize('has-permission', 'people.view.policies');
        if($request->input('policy', null))
            $viewPolicy = ViewPolicy::find($request->input('policy'));
        else
            $viewPolicy = ViewPolicy::first();

        return view('people.view_policies.index', compact('viewPolicy'));
    }

    public function create()
    {
        Gate::authorize('has-permission', 'people.view.policies');
        return view('people.view_policies.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('has-permission', 'people.view.policies');
        $data = $request->validate(
            [
                'name' => 'required|string|max:255',
                'base_role' => ['required', Rule::in(PolicyType::values())],
                'role_id' => 'required|exists:roles,id',
            ], $this->errors());
        $policy = new ViewPolicy();
        $policy->fill($data);
        $policy->save();
        return redirect(route('people.policies.view.index', ['policy' => $policy->id]))
            ->with('success-status', __('people.policies.view.created'));
    }

    public function edit(ViewPolicy $policy)
    {
        Gate::authorize('has-permission', 'people.view.policies');
        return view('people.view_policies.edit', compact('policy'));
    }

    public function update(Request $request, ViewPolicy $policy)
    {
        Gate::authorize('has-permission', 'people.view.policies');
        $data = $request->validate(
            [
                'name' => 'required|string|max:255',
                'base_role' => ['required', Rule::in(PolicyType::values())],
                'role_id' => 'required|exists:roles,id',
            ], $this->errors());
        $policy->fill($data);
        $policy->save();
        return redirect(route('people.policies.view.index', ['policy' => $policy->id]))
            ->with('success-status', __('people.policies.view.updated'));
    }

    public function destroy(ViewPolicy $policy)
    {
        Gate::authorize('has-permission', 'people.view.policies');
        $policy->delete();
        return redirect(route('people.policies.view.index'))
            ->with('success-status', __('people.policies.view.deleted'));
    }

    public function personal()
    {
        $breadcrumb = [ __('people.profile.mine') => route('people.show', ['person' => Auth::user()->id]), __('people.profile.links.settings.personal_view_policy') => "#" ];
        return view('people.view_policies.personal', compact('breadcrumb'));
    }
}
