@extends('layouts.integrations', ['breadcrumb' => $breadcrumb, 'selectedService' => $classesService])

@section('integrator-content')
    <form action="{{ route('integrators.local.classes.update') }}" method="POST">
        @csrf
        @method('PATCH')
        <h4 class="border-bottom mb-3">{{ __('integrators.local.classes.settings') }}</h4>
        <div class="card mb-3">
            <h5 class="card-header">{{ __('integrators.local.classes.settings.widgets') }}</h5>
            <div class="card-body">
                <table class="table table-borderless table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">{{ __('integrators.local.classes.settings.widgets.required') }}</th>
                            <th class="text-center">{{ __('integrators.local.classes.settings.widgets.optional') }}</th>
                            <th class="text-center">{{ __('integrators.local.classes.settings.widgets.block') }}</th>
                            <th>{{ __('integrators.local.classes.settings.widgets') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(((array)$classesService->data->available) as $widgetClass => $widgetName)
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <input
                                                class="form-check-input m-auto"
                                                type="radio"
                                                name="{{ $widgetClass }}"
                                                value="required"
                                                id="required_{{ $widgetClass }}"
                                                @checked(in_array($widgetClass, (array)$classesService->data->required))
                                        />
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <input
                                                class="form-check-input m-auto"
                                                type="radio"
                                                name="{{ $widgetClass }}"
                                                value="optional"
                                                id="optional_{{ $widgetClass }}"
                                                @checked(in_array($widgetClass, (array)$classesService->data->optional))
                                        />
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <input
                                                class="form-check-input m-auto"
                                                type="radio"
                                                name="{{ $widgetClass }}"
                                                value="block"
                                                id="block_{{ $widgetClass }}"
                                                @checked(!in_array($widgetClass, (array)$classesService->data->optional) && !in_array($widgetClass, (array)$classesService->data->required))
                                        />
                                    </div>
                                </td>
                                <td>
                                    {{ $widgetName }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="alert alert-warning mx-2">
                    {{ __('integrators.local.classes.settings.widgets.warning') }}
                </div>
                <div class="row">
                    <button type="submit" class="btn btn-primary col">{{ __('system.settings.update') }}</button>
                </div>
            </div>
        </div>
    </form>
@endsection