@extends('layouts.integrations', ['breadcrumb' => $breadcrumb, 'selectedService' => $classesService])

@section('integrator-content')
    <form action="{{ route('integrators.local.classes.update') }}" method="POST">
        @csrf
        @method('PATCH')
        <h4 class="border-bottom mb-3">{{ __('integrators.local.classes.settings') }}</h4>
        <div class="card mb-3">
            <h5 class="card-header">{{ __('integrators.local.classes.settings.widgets') }}</h5>
            <div class="card-body">
                @foreach(((array)$classesService->data->available_widgets) as $widgetClass => $widgetName)
                    <div class="form-check form-switch">
                        <input
                                class="form-check-input"
                                type="checkbox"
                                name="widgets[]"
                                value="{{ $widgetClass }}"
                                id="{{ $widgetClass }}"
                                @checked(in_array($widgetClass, array_keys((array)$classesService->data->widgets_allowed)))
                                switch
                        />
                        <label class="form-check-label" for="{{ $widgetClass }}">
                            {{ $widgetName }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </form>
@endsection