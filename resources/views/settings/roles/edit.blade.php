@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="border-bottom display-4 text-primary mb-5">{{ __('settings.role.edit') }}</div>
        <div class="alert alert-danger">
            <strong>{{ __('common.warning') }}!</strong> {{ __('settings.role.base.warning') }}
        </div>
        <form action="{{ route('settings.roles.update', ['role' => $role->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('settings.permission.name') }}</label>
                <input
                        type="text"
                        name="name"
                        id="name"
                        @if($role->base_role) disabled @endif
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ $role->name }}"
                />
                <x-error-display key="name">{{ $errors->first('name') }}</x-error-display>
            </div>

            <h5 class="border-bottom">{{ __('settings.permission.assign') }}</h5>
            @error('permissions')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div class="row row-cols-4 mb-3">
                @foreach (\Spatie\Permission\Models\Permission::orderBy('name')->get() as $permission)
                    <div class='col'>
                        <div class="form-check">
                            <input
                                    type="checkbox"
                                    name="permissions[]"
                                    id="permissions_{{ $permission->id }}"
                                    class="form-check-input"
                                    value="{{ $permission->name }}"
                                    @if($role->hasPermissionTo($permission->name)) checked @endif
                            />
                            <label for="permissions_{{ $permission->id }}"
                                   class="form-check-label">{{ $permission->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row">
                <button class="btn btn-primary col-md m-1" type="submit">{{ __('settings.role.edit') }}</button>
                @can('settings.roles.delete')
                    @if(!$role->base_role)
                        <button
                                class="btn btn-danger col-md m-1"
                                type="button"
                                onclick="confirmDelete('{{ __('settings.role.delete.confirm') }}', '{{ route('settings.roles.destroy', ['role' => $role->id]) }}')"
                        >{{ __('settings.role.delete') }}</button>
                    @endif
                @endcan
                <a class="btn btn-secondary col-md m-1" role="button"
                   href="{{ route('settings.roles.index') }}">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
@push('scripts')
    <script>
        tc = new TextCounter('description', 255, 25);
    </script>
@endpush

