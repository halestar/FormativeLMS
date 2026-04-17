@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-7">
                <div class="text-center mb-4">
                    <h1 class="h3 mb-1">{{ __('features.substitutes.verify') }}</h1>
                    <p class="text-muted mb-0">{{ __('features.substitutes.success.description') }}</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5 text-center">
                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill text-success fs-1"></i>
                        </div>

                        <h2 class="h5 mb-2">{{ __('features.substitutes.success.greeting', ['name' => $sub->name]) }}</h2>
                        <p class="text-muted mb-4">{{ __('features.substitutes.success.received') }}</p>

                        <div class="alert alert-success mb-3 text-start" role="alert">
                            {{ __('features.substitutes.success.contact_confirmed') }}
                        </div>

                        @if ($sub->sms_confirmed && filled($sub->phone))
                            <div class="alert alert-light border mb-0 text-start" role="alert">
                                {{ __('features.substitutes.success.sms_confirmed') }}
                                <strong>{{ $sub->phone?->prettyPhone ?? "-" }}</strong>.
                            </div>
                        @else
                            <div class="alert alert-light border mb-0 text-start" role="alert">
                                {{ __('features.substitutes.success.sms_declined') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
