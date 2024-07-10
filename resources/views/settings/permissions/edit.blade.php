@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="border-bottom display-4 text-primary mb-5">{{ __('settings.permission.edit') }}</div>
        <form action="{{ route('settings.permissions.update', ['permission' => $permission->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="input-group mb-3">
                <label for="category_id" class="input-group-text">{{ __('settings.permission.category.select') }}</label>
                <select
                    class="form-select @error('category_id') is-invalid @endif"
                    id="category_id"
                    name="category_id"
                >
                    <option value="">{{ __('settings.permission.category.select') }}...</option>
                    @foreach(\App\Models\Utilities\PermissionCategory::all() as $category)
                        <option value="{{ $category->id }}" @if($permission->category_id == $category->id) selected @endif >{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-error-display key="category_id">{{ $errors->first('category_id') }}</x-error-display>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('settings.permission.name') }}</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ $permission->name }}"
                />
                <x-error-display key="name">{{ $errors->first('name') }}</x-error-display>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('settings.permission.description') }}</label>
                <textarea
                    type="text"
                    name="description"
                    id="description"
                    class="form-control no-resize @error('description') is-invalid @enderror"
                >{{ $permission->description }}</textarea>
                <x-error-display key="description">{{ $errors->first('description') }}</x-error-display>
            </div>

            <h5 class="border-bottom">{{ __('settings.role.assign') }}</h5>
            <div class="row row-cols-4 mb-3">
                @foreach (\Spatie\Permission\Models\Role::where('name', '<>', \App\Models\Utilities\SchoolRoles::$ADMIN)->orderBy('name')->get() as $role)
                    <div class='col'>
                        <div class="form-check">
                            <input
                                type="checkbox"
                                name="roles[]"
                                id="roles_{{ $role->id }}"
                                class="form-check-input"
                                value="{{ $role->name }}"
                                @if($permission->hasRole($role->name)) checked @endif
                            />
                            <label for="roles_{{ $role->id }}" class="form-check-label">{{ $role->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row">
                <button class="btn btn-primary col-md m-1" type="submit">{{ __('settings.permission.edit') }}</button>
                @can('settings.permissions.delete')
                <button
                    class="btn btn-danger col-md m-1"
                    type="button"
                    onclick="confirmDelete('{{ __('settings.permission.delete.confirm') }}', '{{ route('settings.permissions.destroy', ['permission' => $permission->id]) }}')"
                >{{ __('settings.permission.delete') }}</button>
                @endcan
                <a class="btn btn-secondary col-md m-1" role="button" href="{{ route('settings.permissions.index') }}">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
@push('scripts')
    <script>
        tc = new TextCounter('description', 255, 25);
    </script>
@endpush

