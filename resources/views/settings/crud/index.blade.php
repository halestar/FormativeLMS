@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-4">
                <h4 class="border-bottom pb-1 mb-2">{{ __('crud.system_tables') }}</h4>
                <div class="list-group" id="table_container">
                    @foreach(\App\Models\SystemTables\SystemTable::tableModels() as $crudModel)
                        <button
                                id="table_selector_{{ $loop->index }}"
                                class="list-group-item list-group-item-action @if($loop->first) active @endif"
                                @if($loop->first) aria-bs-current="true" @endif
                                onclick="setCrudTable('{{ str_replace("\\", "\\\\", $crudModel) }}', {{ $loop->index }})">
                            {{ $crudModel::getCrudModelName() }}
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="col">
                <livewire:crud-item-update :model="(\App\Models\SystemTables\SystemTable::tableModels()[0])"/>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function setCrudTable(model, selector_id) {
            $('#table_container .list-group-item.active').removeClass('active');
            $('#table_selector_' + selector_id).addClass('active');
            window.Livewire.dispatch('change-crud-model', {model: model})
        }
    </script>
@endpush
