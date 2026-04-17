@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-7">
                <div class="text-center mb-4">
                    <h1 class="h3 mb-1">{{ __('features.substitutes.request.access.rejected.title') }}</h1>
                    <p class="text-muted mb-0">{{ __('features.substitutes.request.access.rejected.description') }}</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5 text-center">
                        <div class="mb-3">
                            <i class="bi bi-x-circle-fill text-danger fs-1"></i>
                        </div>
                        <h2 class="h5 mb-2">{{ __('features.substitutes.request.access.rejected.heading') }}</h2>
                        <p class="text-muted mb-0">
                            {{ __('features.substitutes.request.access.rejected.notice') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
