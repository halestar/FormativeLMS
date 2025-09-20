@extends('layouts.integrations', ['breadcrumb' => $breadcrumb, 'selectedService' => $service])

@section('integrator-content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 mx-auto"
                 x-data="{ disabled: {{ $service->inherit_permissions? 'true': 'false' }} }">
                <livewire:utilities.model-switch
                        :model="$service"
                        property="inherit_permissions"
                        :label="__('integrators.inherit_permissions')"
                        classes="h2 mb-3"
                        on-change="disabled = !disabled; disabled? $dispatch('role-assigner.disable'): $dispatch('role-assigner.enable'); $dispatch('role-assigner.refresh-roles')"
                />
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 mx-auto">
                <livewire:role-assigner :attachObj="$service" editor-only="true"
                                        :disabled="$service->inherit_permissions"/>
            </div>
        </div>
    </div>
@endsection