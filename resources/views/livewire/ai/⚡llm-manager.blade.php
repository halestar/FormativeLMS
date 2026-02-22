<?php

use App\Casts\Ai\ProviderOptions;
use App\Models\Ai\Llm;
use App\Models\Integrations\Connections\AiConnection;
use App\Models\Integrations\IntegrationConnection;
use Illuminate\Support\Collection;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
	#[Locked]
	public string $connection_id;
	public Collection $llms;
	public ?Llm $selectedLlm = null;
	public array $providerOptions = [];
	public string $name = "";
	public bool $hidden = true;
	public ?string $description = null;

	public function mount(string $connection_id)
	{
		$this->connection_id = $connection_id;
		$this->llms = Llm::where('connection_id', $connection_id)->get();
	}

	public function refreshModels()
	{
		IntegrationConnection::find($this->connection_id)->refreshLlms();
		$this->llms = Llm::where('connection_id', $this->connection_id)->get();
	}

	public function resetModels()
	{
		IntegrationConnection::find($this->connection_id)->refreshLlms(true);
		$this->llms = Llm::where('connection_id', $this->connection_id)->get();
	}

	public function selectModel(Llm $model)
	{
		if ($this->llms->where('id', $model->id)->count() > 0)
		{
			$this->selectedLlm = $model;
			$this->name = $model->name;
			$this->hidden = $model->hide;
			$this->description = $model->description;
			$this->providerOptions = $model->provider_options->getOptions();
		}
	}

	public function clearSelection()
	{
		$this->selectedLlm = null;
		$this->name = "";
		$this->hidden = true;
		$this->description = null;
		$this->providerOptions = [];
	}

	protected function rules()
	{
		return
		[
			'name' => 'required|min:1|max:60',
			'description' => 'nullable|max:255',
			'hidden' => 'nullable|boolean',
		];
	}

	public function updateModel()
	{
		//validate the basic options
		$this->validate();
		//next, we validate the ProviderOptions object.
		$connection = IntegrationConnection::find($this->connection_id);
		$errors = false;
		foreach ($this->providerOptions as $option)
		{
			if(!$connection->validProviderOption($this->selectedLlm, $option))
			{
				$this->addError($option->field, "This field is not valid.");
				$errors = true;
			}
		}
		if($errors)
			return;
		//at this point we can update the LLM
		$this->selectedLlm->name = $this->name;
		$this->selectedLlm->description = $this->description;
		$this->selectedLlm->hide = $this->hidden;
		$this->selectedLlm->provider_options = (new ProviderOptions)->addOptions($this->providerOptions);
		$this->selectedLlm->save();
		//clear the editor
		$this->clearSelection();
		//refresh the llms.
		$this->llms = Llm::where('connection_id', $this->connection_id)->get();
	}

	public function sortModels($item, $position)
	{
		$idx = 0;
		foreach($this->llms as $llm)
		{
			if($idx == $position)
				$idx++;

			if($llm->id == $item)
				$llm->order = $position;
			else
				$llm->order = $idx;

			$llm->save();
			$idx++;
		}
		$this->llms = Llm::where('connection_id', $this->connection_id)->get();
	}

};
?>

<div class="card">
	<div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
		<h4 class="mb-0">LLM Models</h4>
		<div class="d-flex flex-wrap gap-2">
			<button
					type="button"
					class="btn btn-sm btn-primary"
					wire:click="refreshModels"
					wire:loading.attr="disabled"
					wire:target="refreshModels,resetModels"
			>Refresh Models
			</button>
			<button
					type="button"
					class="btn btn-sm btn-warning"
					wire:click="resetModels"
					wire:confirm="This will reset all model settings to defaults. Continue?"
					wire:loading.attr="disabled"
					wire:target="refreshModels,resetModels"
			>Reset to Defaults
			</button>
			<div
				class="text-muted small align-self-center"
				wire:loading
				wire:target="refreshModels,resetModels"
			>
				Models are being loaded...
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-md-4">
				<ul class="list-group" wire:sort="sortModels">
					@forelse($llms as $llm)
						<li
								class="show-as-action list-group-item list-group-item-action d-flex justify-content-between align-items-center @if($selectedLlm && data_get($selectedLlm, 'id') === $llm->id) active @endif"
								wire:sort:item="{{ $llm->id }}"
								wire:key="llm-{{ $llm->id }}"
								wire:click="selectModel({{ $llm->id }})"
						>
							<div class="d-flex align-items-center flex-grow-1 me-2 @if($llm->hide) opacity-50 @endif">
								<span class="show-as-grab me-2 text-muted" wire:sort:handle>
									<i class="fa-solid fa-grip-lines-vertical"></i>
								</span>
								<div>
									<div class="fw-semibold">{{ $llm->name ?: $llm->model_id }}</div>
									<small class="text-muted">{{ $llm->description }}</small>
								</div>
							</div>
							@if($llm->hide)
								<span class="badge text-bg-secondary">Hidden</span>
							@endif
						</li>
					@empty
						<li class="list-group-item text-muted">
							No models found. Use refresh to sync from the provider.
						</li>
					@endforelse
				</ul>
			</div>
			<div class="col-md-8">
				@if($selectedLlm)
					<div class="border rounded p-3">
						<div class="row align-items-end">
							<div class="col-md-8 mb-3">
								<label for="llm_model_id" class="form-label">Model ID</label>
								<input
										type="text"
										id="llm_model_id"
										class="form-control"
										value="{{ $selectedLlm->model_id }}"
										disabled
										readonly
								/>
							</div>
							<div class="col-md-4 mb-3 d-flex justify-content-end">
								<div class="form-check form-switch">
									<input
											class="form-check-input"
											type="checkbox"
											role="switch"
											id="llm_hide"
											wire:model="hidden"
									/>
									<label class="form-check-label" for="llm_hide">Hidden</label>
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label for="llm_name" class="form-label">Name</label>
							<input
									type="text"
									id="llm_name"
									class="form-control"
									wire:model="name"
							/>
						</div>
						<div class="mb-3">
							<label for="llm_description" class="form-label">Description</label>
							<textarea
									id="llm_description"
									class="form-control"
									rows="3"
									wire:model="description"
							></textarea>
						</div>

						<h5 class="border-bottom pb-2 mt-4">Provider Options</h5>
						<div class="row row-cols-2">
							@forelse($providerOptions as $option)
								<div class="col mb-3" wire:key="po-{{ $selectedLlm->id }}{{ $option->field }}">
									<label class="form-label"
										   for="provider_option_{{ $option->field }}">{{ $option->title }}</label>
									@switch($option->type)
										@case(\App\Enums\BasicDataInput::NUMBER)
											<input
													type="number"
													id="provider_option_{{ $option->field }}"
													class="form-control"
													wire:model="providerOptions.{{ $option->field }}.value"
											/>
											@break
										@case(\App\Enums\BasicDataInput::FLOAT)
											<input
													type="number"
													step="0.01"
													id="provider_option_{{ $option->field }}"
													class="form-control"
													wire:model="providerOptions.{{ $option->field }}.value"
											/>
											@break
										@case(\App\Enums\BasicDataInput::MULTIPLE_SELECTIONS)
											<div class="d-flex flex-wrap gap-3">
												@foreach($option->choices as $choiceValue => $choice)
													<div class="form-check">
														<input
																class="form-check-input"
																type="checkbox"
																id="{{ $choice . "_" . $choiceValue }}"
																value="{{ $choiceValue }}"
																wire:model="providerOptions.{{ $option->field }}.value"
														/>
														<label class="form-check-label"
															   for="{{ $choice . "_" . $choiceValue  }}">{{ $choice }}</label>
													</div>
												@endforeach
											</div>
											@break
										@case(\App\Enums\BasicDataInput::CHOICE)
											<div class="d-flex flex-wrap gap-3">
												@foreach($option->choices as $choiceValue => $choice)
													<div class="form-check">
														<input
																class="form-check-input"
																type="radio"
																id="{{ $choice . "_" . $choiceValue }}"
																value="{{ $choiceValue }}"
																wire:model="providerOptions.{{ $option->field }}.value"
														/>
														<label class="form-check-label"
															   for="{{ $choice . "_" . $choiceValue }}">{{ $choice }}</label>
													</div>
												@endforeach
											</div>
											@break
										@case(\App\Enums\BasicDataInput::COMBO)
											<select
													id="provider_option_{{ $option->field }}"
													class="form-select"
													wire:model="providerOptions.{{ $option->field }}.value"
											>
												@foreach($option->choices as $choiceValue => $choice)
													<option value="{{ $choiceValue }}">{{ $choice }}</option>
												@endforeach
											</select>
											@break
										@default
											<input
													type="text"
													id="provider_option_{{ $option->field }}"
													class="form-control"
													wire:model="providerOptions.{{ $option->field }}.value"
											/>
									@endswitch
									@if($option->description)
										<div class="form-text">{{ $option->description }}</div>
									@endif
								</div>
							@empty
								<div class="alert alert-info mt-3">No provider options available for this model.</div>
							@endforelse
						</div>

						<div class="row mt-4">
							<button type="button" class="col btn btn-primary mx-2" wire:click="updateModel">
								{{ __('common.update') }}
							</button>
							<button type="button" class="col btn btn-secondary mx-2" wire:click="clearSelection">
								{{ __('common.cancel') }}
							</button>
						</div>
					</div>
				@else
					<div class="alert alert-info">Select a model to edit its settings.</div>
				@endif
			</div>
		</div>
	</div>
</div>
