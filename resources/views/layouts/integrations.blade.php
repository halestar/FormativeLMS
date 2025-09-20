@extends('layouts.app', ['breadcrumb' => $breadcrumb?? $composerBreadcrumb])

@section('content')
    <div class="row position-relative mt-0 gx-0" x-data="{ menuExpanded: {{ $menuOpen? 'true': 'false' }} }">
        <div
                class="rounded border-top border-bottom border-end overflow-y-hidden overflow-x-auto position-relative"
                style="height: 75vh;"
                x-cloak
                :class="menuExpanded ? 'col-lg-3' : 'col-lg-1'"
        >
            <div class="d-flex mt-1 justify-content-end">
                <i
                        class="fs-4 fa-solid ms-auto"
                        :class="{ 'fa-angles-left': menuExpanded, 'fa-angles-right': !menuExpanded }"
                        x-on:click="menuExpanded = !menuExpanded; window.sessionSettings.saveTo('integrationsMenuExpanded', menuExpanded)"
                ></i>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($integrators as $integrator)
                    <li class="list-group-item @if(isset($selectedIntegrator) && $selectedIntegrator->id == $integrator->id) active @endif">
                        <div
                                class="d-flex justify-content-between align-items-center"
                                x-data="{ hovered: false }"
                                @mouseover="hovered = true"
                                @mouseout="hovered = false"
                                :class="{ 'bg-light': hovered }"
                        >
                            @if($integrator->configurable)
                                <a
                                        href="{{ $integrator->configurationUrl() }}"
                                        class="link-underline-opacity-0"
                                        x-cloak
                                        x-show="!menuExpanded"
                                >
                                    @endif
                                    <img
                                            src="{{ $integrator->getImageUrl() }}"
                                            alt="{{ $integrator->name }}"
                                            class="img-fluid img-icon-small rounded @if(!$integrator->configurable) opacity-50 @endif"
                                            x-cloak
                                            x-show="!menuExpanded"
                                    />
                                    @if($integrator->configurable)
                                </a>
                            @endif
                            <h4 x-cloak x-show="menuExpanded">{{ $integrator->name }}</h4>
                            @if($integrator->configurable)
                                <a
                                        href="{{ $integrator->configurationUrl() }}"
                                        class="link-info link-underline-opacity-0"
                                        x-cloak
                                        x-show="menuExpanded"
                                ><i class="fa-solid fa-gear"></i></a>
                            @endif
                        </div>
                        @if($integrator->services()->configurable()->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($integrator->services as $service)
                                    <li
                                            class="list-group-item d-flex justify-content-between align-items-center @if(isset($selectedService) && $selectedService->id == $service->id) active @endif"
                                            x-data="{ hovered: false }"
                                            @mouseover="hovered = true"
                                            @mouseout="hovered = false"
                                            :class="{ 'bg-light rounded': hovered }"
                                    >
                                        <a
                                                href="{{ $service->configurable? $service->configurationUrl(): route('integrators.services.permissions', $service) }}"
                                                class="link-underline-opacity-0"
                                                x-cloak
                                                x-show="!menuExpanded"
                                        >
                                            <img
                                                    src="{{ asset($integrator::$serviceIcons[$service->service_type->value]) }}"
                                                    alt="{{ $service->name }}"
                                                    class="img-fluid img-icon-tiny rounded"
                                            />
                                        </a>
                                        <h5 x-cloak x-show="menuExpanded">{{ $service->name }}</h5>
                                        <div>
                                            <a
                                                    href="{{ route('integrators.services.permissions', $service) }}"
                                                    alt="{{ $service->name }}"
                                                    x-cloak
                                                    x-show="menuExpanded"
                                                    class="link-info link-underline-opacity-0"
                                            ><img src="/images/permissions_icon.svg"
                                                  class="img-fluid img-icon-tiny rounded ms-2"/></a>
                                            @if($service->configurable)
                                                <a
                                                        href="{{ $service->configurationUrl() }}"
                                                        alt="{{ $service->name }}"
                                                        x-cloak
                                                        x-show="menuExpanded"
                                                        class="link-info link-underline-opacity-0"
                                                ><i class="fa-solid fa-gear"></i></a>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
        <div :class="menuExpanded ? 'col-lg-9' : 'col-lg-11'">
            <div class="container">
                @yield('integrator-content')
            </div>
        </div>
    </div>
@endsection