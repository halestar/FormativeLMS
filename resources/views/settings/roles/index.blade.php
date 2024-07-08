@extends('settings.layout', $template)

@section('settings_content')
    <div class="list-group">
        @foreach(\Spatie\Permission\Models\Role::where('name', '<>', \App\Models\Utilities\SchoolRoles::$ADMIN)->orderBy('name')->get() as $role)
            <a
                href="{{ route('settings.roles.edit', ['role' => $role->id]) }}"
                class="list-group-item list-group-item-action"
            >
                <div class="role-name">{{ $role->name }}</div>
                @if($role->permissions()->count() > 0)
                    <strong>Permissions:</strong> {{ $role->permissions()->orderBy('name')->get()->pluck('name')->join(', ') }}
                @endif
            </a>
        @endforeach
    </div>
@endsection
