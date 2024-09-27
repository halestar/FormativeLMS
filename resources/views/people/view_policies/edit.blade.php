@extends('layouts.app', ['breadcrumb' => [__('system.menu.view_policies') => route('people.policies.view.index'),
                    __('common.update') => '#']])

@section('content')
    <div class="container">
        <div
            class="border-bottom display-4 text-primary mb-5">{{ __('people.policies.view.viewable_policy.update') }}</div>
        <form action="{{ route('people.policies.view.update', ['policy' => $policy->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('people.policies.view.viewable_policy.name') }}</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    class="form-control @error('name') is-invalid @enderror @if(old('__token')) is-valid @endif"
                    value="{{ $policy->name }}"
                />
                <x-error-display key="name">{{ $errors->first('name') }}</x-error-display>
            </div>
            <div class="mb-3">
                <label for="base_role"
                       class="form-label">{{ __('people.policies.view.viewable_policy.base_role') }}</label>
                <select name="base_role" id="base_role" class="form-select">
                    @foreach(\App\Enums\PolicyType::cases() as $base_role)
                        <option value="{{ $base_role->value }}" @if($policy->base_role == $base_role) selected @endif>{{ $base_role }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="role_id"
                       class="form-label">{{ __('people.policies.view.viewable_policy.role') }}</label>
                <select name="role_id" id="role_id" class="form-select">
                    @foreach(\App\Models\Utilities\SchoolRoles::whereNotIn('id', \App\Models\People\ViewPolicies\ViewPolicy::whereNot('id', $policy->id)->pluck('role_id')->toArray())->get() as $role)
                        <option value="{{ $role->id }}" @if($policy->role_id == $role->id) selected @endif>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <button type="submit" class="btn btn-primary col mx-2">{{ __('common.update') }}</button>
                <a href="{{ route('people.policies.view.index') }}"
                   class="btn btn-secondary col mx-2">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
