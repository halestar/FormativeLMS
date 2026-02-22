<?php

use App\Classes\Integrators\IntegrationsManager;
use App\Classes\Settings\AiSettings;
use App\Enums\IntegratorServiceTypes;
use App\Models\Ai\Llm;
use App\Models\Integrations\IntegrationConnection;
use Illuminate\Support\Collection;
use Livewire\Component;

new class extends Component
{
	public bool $allow_global_ai;
	public bool $allow_user_ai;
	public bool $capture_ai_queries;
	public bool $allow_prompt_editing;
	public ?IntegrationConnection $default_system_connection;
	public ?Llm $default_model;
	public array $connections;
	public Collection $llms;
	public ?string $connection_id = null;

	public function mount(AiSettings $settings, IntegrationsManager $manager)
	{
		$this->allow_global_ai = $settings->allow_global_ai;
		$this->allow_user_ai = $settings->allow_user_ai;
		$this->capture_ai_queries = $settings->capture_ai_queries;
		$this->allow_prompt_editing = $settings->allow_prompt_editing;
		$availableConnections = $manager->systemConnections(IntegratorServiceTypes::AI);
		foreach ($availableConnections as $connection)
			$this->connections[$connection->id] = $connection->service->name;
		$this->default_system_connection =
				$settings->default_system_connection ?: ($availableConnections->first() ?: null);
		if ($this->default_system_connection)
		{
			$this->connection_id = $this->default_system_connection->id;
			$this->llms = $this->default_system_connection->llms()->available()->get();
			$this->default_model = $settings->default_model ?: ($this->llms->first() ?: null);
		}
	}

	public function setConnection()
	{
		$this->default_system_connection = IntegrationConnection::find($this->connection_id);
		if ($this->default_system_connection)
			$this->llms = $this->default_system_connection->llms()->available()->get();

	}
};
?>

<div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-check">
				<input class="form-check-input" type="checkbox" id="allow_global_ai" name="allow_global_ai"
					   wire:model.live="allow_global_ai" aria-describedby="allow_ai"/>
				<label class="form-check-label" for="allow_global_ai">
					{{ __('system.settings.ai.allow_global_ai') }}
				</label>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-check">
				<input class="form-check-input" type="checkbox" id="allow_user_ai" name="allow_user_ai"
					   wire:model.live="allow_user_ai" aria-describedby="allow_ai"/>
				<label class="form-check-label" for="allow_user_ai">
					{{ __('system.settings.ai.allow_user_ai') }}
				</label>
			</div>
		</div>
	</div>
	<div id="allow_ai"
		 class="form-text">{{ __('system.settings.ai.allow_ai.description') }}</div>
	@if($allow_global_ai || $allow_user_ai)
		<div class="row mt-3">
			<div class="col-md-6">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="capture_ai_queries" name="capture_ai_queries"
						   wire:model.live="capture_ai_queries" aria-describedby="capture_ai_queries_help"/>
					<label class="form-check-label" for="capture_ai_queries">
						{{ __('system.settings.ai.capture_ai_queries') }}
					</label>
					<div id="capture_ai_queries_help"
						 class="form-text">{{ __('system.settings.ai.capture_ai_queries.description') }}</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" name="allow_prompt_editing"
						   id="allow_prompt_editing" wire:model.live="allow_prompt_editing"
						   aria-describedby="allow_prompt_editing_help"/>
					<label class="form-check-label" for="allow_prompt_editing">
						{{ __('system.settings.ai.allow_prompt_editing') }}
					</label>
					<div id="allow_prompt_editing_help"
						 class="form-text">{{ __('system.settings.ai.allow_prompt_editing.description') }}</div>
				</div>
			</div>
		</div>
	@endif
	@if($allow_global_ai)
		<div class="row mt-3">
			<div class="col-md-6">
				<label for="default_system_connection"
					   class="form-label">{{ __('system.settings.ai.default_system_connection') }}</label>
				<select
						id="default_system_connection"
						class="form-select"
						name="default_system_connection"
						wire:model="connection_id"
						wire:change="setConnection"
				>
					@foreach($connections as $connectionId => $connection)
						<option value="{{ $connectionId }}">{{ $connection }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-6">
				<label for="default_model" class="form-label">{{ __('system.settings.ai.default_model') }}</label>
				<select id="default_model" class="form-select" name="default_model">
					@foreach($llms as $llm)
						<option value="{{$llm->id}}" @selected($llm->id == $default_model->id)>
							{{$llm->name}}
						</option>
					@endforeach
				</select>
			</div>
		</div>
	@endif
</div>