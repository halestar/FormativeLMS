<div>
    <div class="row mb-3">
        <div class="col-3 display-6">{{ $role->name . " " . __('people.name') }}:</div>
        <div class="col-7 d-flex flex-wrap justify-content-start align-self-baseline"  wire:sortable="updateOrder">
            @foreach($tokens as $token)
            <div class="p-0 m-2" wire:key="{{ $loop->index }}" wire:sortable.item="{{ $loop->index }}">
                <div class="input-group">
                    <div class="input-group-text" wire:sortable.handle>
                        <i class="fa-solid fa-grip-lines-vertical"></i>
                    </div>
                    @if($token->type == \App\Classes\NameToken::TYPE_BASIC_FIELD)
                    <select class="form-select form-select-sm"
                            wire:change="updateBasicFieldName({{ $loop->index }}, $event.target.value)">
                        @foreach(\App\Classes\NameToken::basicFields() as $field => $fieldName)
                            <option
                                value="{{ $field }}" @selected($field == $token->basicFieldName)>{{ $fieldName }}</option>
                        @endforeach
                    </select>
                    @elseif($token->type == \App\Classes\NameToken::TYPE_ROLE_FIELD)
                        <select class="form-select form-select-sm" wire:change="updateRoleField({{ $loop->index }}, $event.target.value)">
                            @foreach($role->fields as $field)
                                <option value="{{ $field->fieldId }}">{{ $field->fieldName }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" value="{{ $token->textContent }}" class="form-control form-control-sm" wire:change="updateText({{ $loop->index }}, $event.target.value)" />
                    @endif
                    <button
                        type="button"
                        class="btn @if($token->spaceAfter) btn-success @else btn-danger @endif"
                        wire:click="updateSpaceAfter({{ $loop->index }}, {{ $token->spaceAfter? 0: 1 }})"
                    >
                        {{ __('people.name.creator.space') }}
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="removeToken({{ $loop->index }})"><i
                            class="fa fa-times"></i></button>
                </div>
            </div>
            @endforeach
        </div>
        <div class="col-2">
            <div class="d-grid gap-1 ms-auto">
                <button type="button" class="btn btn-outline-primary btn-sm text-start" wire:click="addBasicToken()"><span
                        class="border-end me-2 pe-2"><i class="fa fa-plus"></i></span>{{ __('people.name.creator.basic') }}
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm text-start" wire:click="addRoleToken()"><span
                        class="border-end me-2 pe-2"><i
                            class="fa fa-plus"></i></span>{{ __('people.name.creator.role') }}
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm text-start" wire:click="addTextToken()"><span
                        class="border-end me-2 pe-2"><i
                            class="fa fa-plus"></i></span>{{ __('people.name.creator.text') }}
                </button>
            </div>
        </div>
    </div>
    <div class="alert alert-info mb-3 d-flex justify-content-between align-items-center">
        <div class="display-6">{{ __('people.name.creator.sample') }}</div>
        <div class="display-6">{{ $sampleName }}</div>
        <button type="button" class="btn btn-primary" wire:click="newSamplePerson()"></button>
    </div>
    <div class="row">
        <button type="button" class="col mx-2 btn btn-primary" wire:click="saveName()">{{ __('people.name.creator.random') }}</button>
        <button type="button" class="col mx-2 btn btn-secondary" wire:click="resetName()">{{ __('people.name.creator.random') }}</button>
    </div>
</div>
