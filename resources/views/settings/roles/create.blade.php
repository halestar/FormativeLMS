@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="border-bottom display-4 text-primary mb-5">{{ __('settings.role.new') }}</div>
        <form action="{{ route('settings.roles.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('settings.role.name') }}</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    class="form-control @error('name') is-invalid @enderror @if(old('__token')) is-valid @endif"
                    value="{{ old('name') }}"
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
                            />
                            <label for="permissions_{{ $permission->id }}" class="form-check-label">{{ $permission->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row">
                <button class="btn btn-primary col m-1" type="submit">{{ __('settings.role.new') }}</button>
                <a class="btn btn-secondary col-md m-1" role="button" href="{{ route('settings.roles.index') }}">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
@push('scripts')
    <script>
        tc = new TextCounter('description', 255, 25);
    </script>
@endpush
