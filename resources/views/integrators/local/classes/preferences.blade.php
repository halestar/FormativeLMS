@extends('layouts.class-settings', ['breadcrumb' => $breadcrumb, 'classSelected' => $classSelected])

@section('class_settings_content')
    <form
        x-data="{ enabled: {{ $prefs['enabled'] ? 'true' : 'false' }} }"
        action="{{ route('integrators.local.services.classes.preferences.update', $classSelected) }}"
        method="post"
    >
        @csrf
        <h3 style="color: {{ $classSelected->subject->getTextHex() }}" class="mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" value="1" name="enabled" id="enabled" switch x-model="enabled">
                <label class="form-check-label mt-1" for="enabled">
                    {{ __('integrators.local.classes.preferences.tabs') }}
                </label>
            </div>
        </h3>
        @foreach($service->data->available as $widgetClass => $widget)
            @continue(!in_array($widgetClass, $service->data->required) && !in_array($widgetClass, $service->data->optional))
            <div class="form-check mb-3">
                <input
                        class="form-check-input"
                        type="checkbox"
                        value="{{ $widgetClass }}"
                        name="widgets[]"
                        id="{{ $widgetClass }}"
                        @if(in_array($widgetClass, $service->data->required))
                            checked
                            disabled
                        @else
                            :disabled="!enabled"
                            @checked($prefs['enabled'] && in_array($widgetClass, $prefs['widgets']))
                        @endif
                />
                <label class="form-check-label" for="{{ $widgetClass }}">
                    {{ $widget }}
                </label>
            </div>
        @endforeach
        <button type="submit" class="btn btn-primary">{{ __('common.apply') }}</button>
    </form>
@endsection