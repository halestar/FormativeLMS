<div>
    @if($editing || $adding)
        <div class="border rounded p-2 text-bg-light">
            <div class="row">
                <div class="col-md-10">
                    <div class="form-floating mb-0">
                        <input
                            type="text"
                            class="form-control"
                            id="phone"
                            placeholder="{{ __('phones.phone_number') }}"
                            autocomplete="off"
                            wire:model.live.debounce="phone"
                        />
                        <label for="phone">{{ __('phones.phone') }}</label>
                    </div>
                    @if($suggestedPhones && $suggestedPhones->count() > 0)
                        <div class="absolute m-0 rounded w-full bg-gray-200 pl-2">
                            <ul class="list-group">
                                @foreach($suggestedPhones as $suggestion)
                                    <li
                                        class="list-group-item list-group-item-action"
                                        wire:key="{{ $suggestion->id }}"
                                        wire:click="setLinking({{ $suggestion->id }})"
                                    >
                                        {{ $suggestion->prettyPhone }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-0">
                        <input
                            type="text"
                            class="form-control"
                            id="ext"
                            placeholder="{{ __('phones.ext') }}"
                            autocomplete="off"
                            wire:model.live.debounce="ext"
                        />
                        <label for="ext">{{ __('phones.ext') }}</label>
                    </div>
                </div>
                <div class="col-3 mt-3">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="mobile"
                            wire:model="mobile"
                        />
                        <label class="form-check-label" for="mobile">
                            {{ __('phones.mobile_phone') }}
                        </label>
                    </div>
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="primary"
                            wire:model="primary"
                        />
                        <label class="form-check-label" for="primary">
                            {{ __('phones.primary_phone') }}
                        </label>
                    </div>
                </div>
                <div class="col-3 mt-3">
                    <div class="form-floating mb-0">
                        <input
                            type="text"
                            class="form-control"
                            id="label"
                            placeholder="{{ __('phones.label') }}"
                            autocomplete="off"
                            wire:model.live.debounce="label"
                        />
                        <label for="label">{{ __('phones.label') }}</label>
                    </div>
                </div>
                <div class="col-md-6 mt-3 align-self-center text-end">
                    @if($adding)
                        <button
                            type="button"
                            class="btn btn-primary btn-lg me-2"
                            wire:click="addPhone()"
                        >{{ __('phones.add_phone') }}</button>
                    @else
                        <button
                            type="button"
                            class="btn btn-primary btn-lg me-2"
                            wire:click="updatePhone()"
                        >{{ __('phones.update_phone') }}</button>
                    @endif
                    <button
                        type="button"
                        class="btn btn-secondary btn-lg"
                        wire:click="clearForm()"
                    >{{ __('common.cancel') }}</button>
                </div>
            </div>
        </div>
    @elseif($linking)

    @else
        @if($phones->first() && $phones->first()->personal->primary)
            <ul class="list-group mb-1 shadow ">
                <li class="list-group-item text-bg-secondary border-2 border-primary-subtle" wire:key="{{ $phones->first()->id }}">
                    <div class=" d-flex justify-content-between align-items-center">
                        <div>
                            <strong>
                                {{ __('addresses.primary') }}
                                @if($phones->first()->mobile){{ __('phones.mobile') }} @endif
                                @if($phones->first()->personal->label){{ $phones->first()->personal->label }}@endif
                                {{ __('phones.phone') }}
                            </strong>
                            {!! $phones->first()->prettyPhone !!}
                        </div>
                        <div>
                            <button
                                type="button"
                                class="btn btn-sm btn-primary p-1"
                                wire:click="editPhone({{ $phones->first()->id }})"
                            ><i class="fa fa-edit"></i></button>
                            <button
                                type="button"
                                class="btn btn-sm btn-danger p-1"
                                wire:confirm="Are you sure you wish to unlink this phone?"
                                wire:click="removePhone({{ $phones->first()->id }})"
                            ><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                </li>
            </ul>
        @endif
        <ul class="list-group" wire:sortable="updatePhoneOrder">
            @foreach($phones as $phone)
                @if($loop->first && $phone->personal->primary)
                    @continue
                @endif
                <li class="list-group-item text-bg-secondary" wire:key="{{ $phone->id }}" wire:sortable.item="{{ $phone->id }}">
                    <div class=" d-flex justify-content-between align-items-center">
                        <div>
                            @if($loop->first)
                                <span class="me-2">&nbsp;</span>
                            @else
                            <span wire:sortable.handle class="show-as-action me-2"><i class="fa-solid fa-grip-lines-vertical"></i></span>
                            @endif
                            <strong>
                                @if($phone->personal->primary){{ __('addresses.primary') }} @endif
                                @if($phone->mobile){{ __('phones.mobile') }} @endif
                                @if($phone->personal->label){{ $phone->personal->label }}@endif
                                {{ __('phones.phone') }}
                            </strong>
                                {!! $phone->prettyPhone !!}
                        </div>
                        <div>
                            <button
                                type="button"
                                class="btn btn-sm btn-primary p-1"
                                wire:click="editPhone({{ $phone->id }})"
                            ><i class="fa fa-edit"></i></button>
                            <button
                                type="button"
                                class="btn btn-sm btn-danger p-1"
                                wire:confirm="Are you sure you wish to unlink this phone?"
                                wire:click="removePhone({{ $phone->id }})"
                            ><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="d-flex justify-content-center">
            <button
                class="btn btn-success mx-auto"
                wire:click="set('adding', true)"
            ><i class="fa fa-plus border-end pe-1 me-1"></i>{{ __('phones.add_phone') }}</button>
        </div>
    @endif
</div>
