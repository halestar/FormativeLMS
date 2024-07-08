@extends('settings.layout', $template)

@section('settings_content')
    <div class="accordion accordion-flush mt-2" id="permissions_container">
        @foreach(\App\Models\Utilities\PermissionCategory::all() as $category)
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button
                        class="accordion-button collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#cat_{{ $category->id }}"
                        aria-expanded="false"
                        aria-controls="cat_{{ $category->id }}"
                    >
                        {{ $category->name }}
                    </button>
                </h3>
                <div id="cat_{{ $category->id }}" class="accordion-collapse collapse" data-bs-parent="#permissions_container">
                    <div class="accordion-body">
                        <div class="list-group list-group-flush">
                        @foreach($category->permissions as $permission)
                            <a
                                href="{{ route('settings.permissions.edit', ['permission' => $permission->id]) }}"
                                class="list-group-item list-group-item-action"
                            >
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="permission-name">{{ $permission->name }}</span>
                                    <span class="text-muted text-sm">{{ $permission->description }}</span>
                                </div>
                                @if($permission->roles()->count() > 0)
                                <strong>Roles:</strong> {{ $permission->roles->pluck('name')->join(', ') }}
                                @endif
                            </a>
                        @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
