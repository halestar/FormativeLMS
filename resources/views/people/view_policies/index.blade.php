@extends('layouts.app', ['breadcrumb' => [__('system.menu.view_policies') => '#']])

@section('content')
    <div class="container">
        <form action="{{ route('people.policies.view.index') }}" method="GET" id="policy-form">
            <div class="input-group mb-5">
                <label for="policy" class="input-group-text">{{ __('people.policies.view.viewable_policy') }}</label>
                <select class="form-select" name="policy" id="policy" onchange="$('#policy-form').submit()">
                    @foreach(\App\Models\People\ViewPolicies\ViewPolicy::all() as $policy)
                        <option value="{{ $policy->id }}"
                                @if($viewPolicy->id == $policy->id) selected @endif >{{ $policy->name }}</option>
                    @endforeach
                </select>
                <a role="button" class="btn btn-primary"
                   href="{{ route('people.policies.view.create') }}">{{ __('common.new') }}</a>
                @if(!$viewPolicy->isBasePolicy())
                    <a href="{{ route('people.policies.view.edit', ['policy' => $viewPolicy->id]) }}" role="button" class="btn btn-secondary">{{ __('common.edit') }}</a>
                    <button
                        onclick="confirmDelete('{{ __('people.policies.view.deleted.confirm') }}', '{{ route('people.policies.view.destroy', ['policy' => $viewPolicy->id])  }}')"
                        type="button"
                        class="btn btn-danger"
                    >{{ __('common.delete') }}</button>
                @endif
            </div>
        </form>
        <livewire:view-permission-editor :viewPolicy="$viewPolicy"/>
    </div>
@endsection
