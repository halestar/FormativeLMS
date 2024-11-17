@extends('settings.layout', $template)

@section('settings_content')
    <div class="list-group list-group-flush m-0">
        @foreach(\App\Models\Utilities\SchoolRoles::all() as $role)
            <a
                @can('settings.roles.edit')
                href="{{ route('settings.roles.edit', ['role' => $role->id]) }}"
                @else
                    href="#"
                @endcan
                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
            >
                <div>
                    <div class="role-name">
                        {{ $role->name }}
                    </div>
                    @if($role->permissions()->count() > 0)
                        <strong>{{ __('settings.permissions') }}:</strong> {{ $role->permissions()->orderBy('name')->get()->pluck('name')->join(', ') }}
                    @endif
                </div>
                @if($role->base_role)
                    <span class="badge bg-danger ms-auto">{{ __('settings.role.base') }}</span>
                @endif
            </a>
        @endforeach
    </div>
@endsection
