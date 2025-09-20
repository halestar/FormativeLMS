@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <ul class="list-group">
                    @foreach(\App\Models\Integrations\Integrator::all() as $integrator)
                        <li class="list-group-item">
                            <div class="row mb-2">
                                <div class="col-md-3 text-center">
                                    <img
                                            src="{{ $integrator->getImageUrl() }}"
                                            alt="{{ $integrator->name }}"
                                            class="img-fluid w-100"
                                    />
                                </div>
                                <div class="col-md-9">
                                    <div class="border-bottom my-2 d-flex justify-content-between">
                                        <h5>{{ $integrator->name }} <small>(v.{{ $integrator->version }})</small></h5>
                                        <livewire:utilities.model-switch
                                                :model="$integrator"
                                                property="enabled"
                                        />
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 d-flex flex-column justify-content-center">
                                            <div class="mb-3">
                                                <livewire:role-assigner :attach-obj="$integrator"/>
                                            </div>

                                        @if($integrator->configurable)
                                                <a
                                                    class="btn btn-primary btn-sm text-lowercase mb-1"
                                                    href="{{ $integrator->configurationUrl() }}"
                                                >{{ __('integrators.configure') }}</a>
                                            @endif
                                            <a
                                                class="btn btn-primary btn-sm text-lowercase mb-1"
                                                href="{{ route('integrators.register', $integrator) }}"
                                            >{{ __('integrators.register.update') }}</a>
                                            <a
                                                    class="btn btn-danger btn-sm text-lowercase mb-1"
                                                    href="{{ route('integrators.clear', $integrator) }}"
                                            >{{ __('integrators.register.clear') }}</a>
                                        </div>
                                        <div class="col-lg-6">
                                            <h6 class="text-decoration-underline">{{ trans_choice('integrators.services', $integrator->services()->count()) }}</h6>
                                            <ul class="list-group list-group-flush">
                                                @foreach($integrator->services as $service)
                                                    <li class="list-group-item">
                                                        <div class="row border-bottom border-light">
                                                            <strong class="col-8">{{ $service->name }}</strong>
                                                            <div class="col-2 mx-auto text-center">
                                                                @if($service->configurable)
                                                                <a href="{{ $service->configurationUrl() }}"
                                                                  class="link-underline-opacity-0">
                                                                    <i class="fa-solid fa-gear"></i>
                                                                </a>
                                                                @endif
                                                                <a href="{{ route('integrators.services.permissions', $service)}}"
                                                                   class="link-underline-opacity-0">
                                                                    <img src="/images/permissions_icon.svg" class="img-fluid img-icon-tiny rounded ms-2" />
                                                                </a>
                                                            </div>
                                                            <livewire:utilities.model-switch
                                                                    :model="$service"
                                                                    property="enabled"
                                                                    classes="col-2 ms-auto"
                                                            />
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection