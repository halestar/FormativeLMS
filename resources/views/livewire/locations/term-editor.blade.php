<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h4>{{ __('locations.terms.campus', ['campus' => $campus->abbr]) }}</h4>
        <button
            type="button"
            class="btn btn-primary btn-sm align-self-center"
            @if($editing || $adding) disabled @endif
            wire:click="set('adding', true)"
        ><i class="fa-solid fa-plus me-2 pe-2 border-end"></i>{{ __('locations.terms.add') }}</button>
    </div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            @if($adding)
                <li class="list-group-item px-0 pt-0 m-0">
                    <form wire:submit="addTerm">
                        <div class="row gx-1 align-items-center p-0 m-0">
                            <div class="col-3">
                                <div class="form-floating">
                                    <input
                                        type="text"
                                        class="form-control @error('label') is-invalid @enderror"
                                        id="label"
                                        wire:model="label"
                                        placeholder="{{ __('locations.terms.label') }}"
                                    />
                                    <label for="name">{{ __('locations.terms.label') }}</label>
                                    <x-error-display key="label">{{ $errors->first('label') }}</x-error-display>
                                </div>
                            </div>
                            <div class="col-3 text-center">
                                <div  class="form-floating">
                                    <input
                                        type="date"
                                        class="form-control @error('term_start') is-invalid @enderror"
                                        id="term_start"
                                        wire:model="term_start"
                                        min="{{ $year->year_start->format('Y-m-d') }}"
                                        max="{{ $year->year_end->format('Y-m-d') }}"
                                        placeholder="{{ __('locations.terms.start') }}"
                                    />
                                    <label for="term_start">{{ __('locations.terms.start') }}</label>
                                    <x-error-display key="term_start">{{ $errors->first('term_start') }}</x-error-display>
                                </div>
                            </div>
                            <div class="col-3 text-center">
                                <div  class="form-floating">
                                    <input
                                        type="date"
                                        class="form-control @error('term_end') is-invalid @enderror"
                                        id="term_end"
                                        wire:model="term_end"
                                        min="{{ $year->year_start->format('Y-m-d') }}"
                                        max="{{ $year->year_end->format('Y-m-d') }}"
                                        placeholder="{{ __('locations.terms.end') }}"
                                    />
                                    <label for="term_end">{{ __('locations.terms.end') }}</label>
                                    <x-error-display key="term_end">{{ $errors->first('term_end') }}</x-error-display>
                                </div>
                            </div>
                            <div class="col-3 text-end">
                                <button type="submit" class="btn btn-primary btn-sm">{{ __('common.add') }}</button>
                                <button
                                    type="button"
                                    wire:click="clearForm" class="btn btn-danger btn-sm"
                                >{{ __('common.cancel') }}</button>
                            </div>
                        </div>
                    </form>
                </li>
            @endif
            @foreach($terms as $term)
                <li class="list-group-item list-group-item-action" wire:key="{{ $term->id }}">
                    @if($editing && $editing == $term->id)
                        <form wire:submit="updateTerm">
                            <div class="row gx-1 align-items-center p-0 m-0">
                                <div class="col-3">
                                    <div class="form-floating">
                                        <input
                                            type="text"
                                            class="form-control @error('label') is-invalid @enderror"
                                            id="label"
                                            wire:model="label"
                                            placeholder="{{ __('locations.terms.label') }}"
                                        />
                                        <label for="name">{{ __('locations.terms.label') }}</label>
                                        <x-error-display key="label">{{ $errors->first('label') }}</x-error-display>
                                    </div>
                                </div>
                                <div class="col-3 text-center">
                                    <div  class="form-floating">
                                        <input
                                            type="date"
                                            class="form-control @error('term_start') is-invalid @enderror"
                                            id="term_start"
                                            wire:model="term_start"
                                            min="{{ $year->year_start->format('Y-m-d') }}"
                                            max="{{ $year->year_end->format('Y-m-d') }}"
                                            placeholder="{{ __('locations.terms.start') }}"
                                        />
                                        <label for="term_start">{{ __('locations.terms.start') }}</label>
                                        <x-error-display key="term_start">{{ $errors->first('term_start') }}</x-error-display>
                                    </div>
                                </div>
                                <div class="col-3 text-center">
                                    <div  class="form-floating">
                                        <input
                                            type="date"
                                            class="form-control @error('term_end') is-invalid @enderror"
                                            id="term_end"
                                            wire:model="term_end"
                                            min="{{ $year->year_start->format('Y-m-d') }}"
                                            max="{{ $year->year_end->format('Y-m-d') }}"
                                            placeholder="{{ __('locations.terms.end') }}"
                                        />
                                        <label for="term_end">{{ __('locations.terms.end') }}</label>
                                        <x-error-display key="term_end">{{ $errors->first('term_end') }}</x-error-display>
                                    </div>
                                </div>
                                <div class="col-3 text-end">
                                    <button type="submit" class="btn btn-primary btn-sm">{{ __('common.update') }}</button>
                                    <button
                                        type="button"
                                        wire:click="clearForm" class="btn btn-danger btn-sm"
                                    >{{ __('common.cancel') }}</button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="row align-items-center">
                            <div class="col-3">
                                {{ $term->label }}
                            </div>
                            <div class="col-3 text-center">
                                {{ $term->term_start->format(config('lms.date_format')) }}
                            </div>
                            <div class="col-3 text-center">
                                {{ $term->term_end->format(config('lms.date_format')) }}
                            </div>
                            <div class="col-3 text-end">
                                <button
                                    class="btn btn-primary btn-sm"
                                    wire:click="loadEdit({{ $term->id }})"
                                    @if($adding || $editing) disabled @endif
                                ><i class="fa-solid fa-edit"></i></button>
                                <button
                                    class="btn btn-danger btn-sm"
                                    @if($adding || $editing || !$term->canDelete()) disabled @endif
                                    wire:click="deleteTerm({{ $term->id }})"
                                    wire:confirm="{{ __('locations.terms.delete.confirm') }}"
                                ><i class="fa-solid fa-times"></i></button>
                            </div>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>
