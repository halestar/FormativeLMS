<div>
    @if($editing)
        <form wire:submit="updateRelationship">
            <div class="border rounded p-2 text-bg-light">
                <div class="input-group">
                    <label class="input-group-text">
                        {{ __('people.is_a', ['name' => $person->name]) }}
                    </label>
                    <select
                            class="form-select"
                            id="relationship_id"
                            wire:model.live="relationship_id"
                    >
                        {!! \App\Models\CRUD\Relationship::htmlOptions() !!}
                    </select>
                    <label class="input-group-text">
                        {!! __('common.to') !!} &nbsp;
                        <a href="{{ route('people.show', ['person' => $editing->school_id]) }}" target="_new">
                            {{ $editing->name }}
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </label>
                </div>
                @if($this->reciprocal)
                    <h5 class="mt-3">{{ __('people.update_reciprocal') }}</h5>
                    <div class="input-group">
                        <label class="input-group-text">
                            {{ __('people.is_a', ['name' => $editing->name]) }}
                        </label>
                        <select
                                class="form-select"
                                id="reciprocal_id"
                                wire:model.live="reciprocal_id"
                        >
                            {!! \App\Models\CRUD\Relationship::htmlOptions() !!}
                        </select>
                        <label class="input-group-text">
                            {{ __('people.to2', ['name' => $person->name]) }}
                        </label>
                    </div>
                @endif
                <div class="text-end mt-3">
                    <button
                            type="submit"
                            class="btn btn-primary"
                    >{{ __('people.update_relation') }}</button>
                    <button
                            type="button"
                            class="btn btn-secondary"
                            wire:click="clearForm()"
                    >{{ __('common.cancel') }}</button>
                </div>
            </div>
        </form>
    @elseif($adding)
        <form wire:submit="addRelationship">
            <div class="border rounded p-2 text-bg-light">
                <div class="input-group">
                    <label class="input-group-text">
                        {{ __('people.is_a', ['name' => $person->name]) }}
                    </label>
                    <select
                            class="form-select"
                            id="relationship_id"
                            wire:model.live="relationship_id"
                    >
                        {!! \App\Models\CRUD\Relationship::htmlOptions() !!}
                    </select>
                    @if(!$to_person_id)
                        <label class="input-group-text">
                            {{ __('common.to') }}
                        </label>
                        <input
                                type="text"
                                id="relation_search"
                                class="form-control"
                                wire:model.live.debounce="relation_search"
                                placeholder="{{ __('people.search_for_person') }}"
                                autocomplete="off"
                        />
                    @else
                        <label class="input-group-text">
                            {!! __('common.to') !!} &nbsp;
                            <a href="{{ route('people.show', ['person' => $to_person_id]) }}" target="_new">
                                {{ $to_person->name }}
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                        </label>
                        <button type="button" class="btn btn-danger" wire:click="clearToPerson()"><i
                                    class="fa fa-times"></i></button>
                    @endif
                </div>
                @if($suggestedPeople && $suggestedPeople->count() > 0)
                    <div class="absolute m-0 rounded w-full bg-gray-200 pl-2">
                        <ul class="list-group">
                            @foreach($suggestedPeople as $suggestion)
                                <li
                                        class="list-group-item list-group-item-action"
                                        wire:key="{{ $suggestion->id }}"
                                        wire:click="linkTarget({{ $suggestion->school_id }})"
                                >
                                    <div class="row">
                                        <div class="col-md-2">
                                            <img
                                                    class='img-fluid img-thumbnail'
                                                    style="height: {{ config('lms.thumb_max_height') }}px !important;"
                                                    src='{{ $suggestion->thumbnail_url }}'
                                                    alt='{{ __('people.profile.image') }}'
                                            />
                                        </div>
                                        <h3 class="col-md-7 align-self-center ">{{ $suggestion->name }}</h3>
                                        <div class="col-3 align-self-center text-end">
                                            @if($suggestion->isStudent())
                                                <span class="badge text-bg-primary">{{ __('common.student') }}</span>
                                            @endif
                                            @if($suggestion->isParent())
                                                <span class="badge text-bg-primary">{{ __('common.parent') }}</span>
                                            @endif
                                            @if($suggestion->isEmployee())
                                                <span class="badge text-bg-primary">{{ trans_choice('people.employee', 1) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if($to_person)
                    <h5 class="mt-3">{{ __('people.add_reciprocal') }}</h5>
                    <div class="input-group">
                        <div class="input-group-text">
                            <input class="form-check-input mt-0" type="checkbox" wire:model="reciprocal">
                        </div>
                        <label class="input-group-text">
                            {{ __('people.is_a', ['name' => $to_person->name]) }}
                        </label>
                        <select
                                class="form-select"
                                id="reciprocal_id"
                                wire:model="reciprocal_id"
                        >
                            {!! \App\Models\CRUD\Relationship::htmlOptions() !!}
                        </select>
                        <label class="input-group-text">
                            {{ __('people.to2', ['name' => $person->name]) }}
                        </label>
                    </div>
                @endif
                <div class="text-end mt-3">
                    @if($to_person_id)
                        <button
                                type="submit"
                                class="btn btn-primary"
                        >{{ __('people.establish_relation') }}</button>
                    @endif
                    <button
                            type="button"
                            class="btn btn-secondary"
                            wire:click="clearForm()"
                    >{{ __('common.cancel') }}</button>
                </div>
            </div>
        </form>
    @else
        @foreach($relations as $relation)
            <div class="border rounded mb-1 p-2 text-bg-light d-flex justify-content-between align-items-center">
                <div>
                    {!! __('people.is_a_to', ['name' => $person->name, 'expr' => $relation->personal->relationship? $relation->personal->relationship->name: "?", 'route' => route('people.edit', ['person' => $relation->id]), 'name_2' => $relation->name]) !!}
                </div>
                <div>
                    <button
                            type="button"
                            class="btn btn-primary btn-sm"
                            wire:click="setEditing({{ $relation->school_id }})"
                    ><i class="fa fa-edit"></i></button>
                    <button
                            type="button"
                            class="btn btn-warning btn-sm"
                            title="{{ __('people.delete_relationship_oneway') }}"
                            wire:click="deleteRelationship({{$relation->id}})"
                            wire:confirm="Are you sure you wish to delete this relationship one way? The reciprocal relationship will not be affected"
                    ><i class="fa fa-times"></i></button>
                    <button
                            type="button"
                            class="btn btn-danger btn-sm"
                            title="{{ __('people.delete_relationship_and_its_reciprocal') }}"
                            wire:confirm="Are you sure you wish to delete this relationship and its reciprocal"
                            wire:click="deleteRelationship({{$relation->id}}, true)"
                    ><i class="fa fa-times"></i></button>
                </div>
            </div>
        @endforeach
        <div class="d-flex justify-content-center">
            <button
                    class="btn btn-success mx-auto"
                    wire:click="set('adding', true)"
            ><i class="fa fa-plus border-end pe-1 me-1"></i>{{ __('people.add_new_relationship') }}</button>
        </div>
    @endif
</div>
