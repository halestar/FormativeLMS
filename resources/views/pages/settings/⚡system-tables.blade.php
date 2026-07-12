<?php

use App\Classes\SessionSettings;
use App\Models\SystemTables\SystemTable;
use App\Traits\FullPageComponent;
use Livewire\Component;

new class extends Component
{
	use FullPageComponent;

	public string $selectedModel;
	public array $systemTables;

	public function mount()
	{
		$this->authorize('has-permission', 'system.tables');
		$this->breadcrumb =
			[
				__('system.menu.system.tables') => route('settings.school'),
			];
		$this->systemTables = SystemTable::tableModels();
		$this->selectedModel = SessionSettings::get('system_tables.selected_model', $this->systemTables[0] ?? '');
	}

	public function updateModel(string $model)
	{
		if (in_array($model, $this->systemTables))
		{
			$this->selectedModel = $model;
			SessionSettings::set('system_tables.selected_model', $this->selectedModel);
		}
	}

};
?>
<div class="container">
    <div class="row gx-4">
        <div class="col-md-4 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                    <h5 class="mb-0 text-primary fw-bold"><i
                                class="fa-solid fa-list me-2"></i>{{ __('crud.system_tables') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="table_container">
                        @foreach($systemTables as $systemTable)
                            <button
                                    class="list-group-item list-group-item-action border-0 px-4 py-3 @if($selectedModel == $systemTable) active bg-primary text-white @endif"
                                    wire:click="updateModel('{!! str_replace("\\", "\\\\", $systemTable) !!}')"
                            >
                                {{ $systemTable::getCrudModelName() }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 col-lg-9">
            <livewire:settings.system-table wire:model="selectedModel"/>
        </div>
    </div>
</div>