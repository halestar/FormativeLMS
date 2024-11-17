<div>
    @if($adding || $editing)
        <div class="border rounded p-2 text-bg-light">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-floating mb-0">
                        <input
                            type="text"
                            class="form-control"
                            id="line1"
                            placeholder="{{ __('addresses.street_address') }}"
                            autocomplete="off"
                            wire:model.live.debounce="line1"
                        />
                        <label for="line1">{{ __('addresses.address_line_1') }}</label>
                    </div>
                    @if($suggestedAddresses && $suggestedAddresses->count() > 0)
                        <div class="absolute m-0 rounded w-full bg-gray-200 pl-2">
                            <ul class="list-group">
                                @foreach($suggestedAddresses as $suggestion)
                                    <li
                                        class="list-group-item list-group-item-action"
                                        wire:key="{{ $suggestion->id }}"
                                        wire:click="setLinking({{ $suggestion->id }})"
                                    >
                                        {{ $suggestion->prettyAddress }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="col-md-12 mt-3">
                    <div class="form-floating mb-3">
                        <input
                            type="text"
                            class="form-control"
                            id="line2"
                            placeholder="{{ __('addresses.address_line_2') }}"
                            wire:model="line2"
                        />
                        <label for="line2">{{ __('addresses.address_line_2') }}</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <input
                            type="text"
                            class="form-control"
                            id="line3"
                            placeholder="{{ __('addresses.address_line_3') }}"
                            wire:model="line3"
                        />
                        <label for="line3">{{ __('addresses.address_line_3') }}</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input
                            type="text"
                            class="form-control"
                            id="city"
                            placeholder="{{ __('addresses.city') }}"
                            wire:model="city"
                        />
                        <label for="city" class="form-label">{{ __('addresses.city') }}</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <input
                            type="text"
                            class="form-control"
                            id="state"
                            placeholder="{{ __('addresses.state') }}"
                            wire:model="state"
                        />
                        <label for="state" class="form-label">{{ __('addresses.state') }}</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <input
                            type="text"
                            class="form-control"
                            id="zip"
                            placeholder="{{ __('addresses.zip') }}"
                            wire:model="zip"
                        >
                        <label for="zip" class="form-label">{{ __('addresses.zip') }}</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <input
                            type="text"
                            class="form-control"
                            id="country"
                            placeholder="{{ __('addresses.country') }}"
                            wire:model="country"
                        />
                        <label for="country" class="form-label">{{ __('addresses.country') }}</label>
                    </div>
                </div>
                @if(!$singleAddressable)
                <div class="col-md-4">
                    <div class="form-floating mb-0">
                        <input
                            type="text"
                            class="form-control"
                            id="label"
                            placeholder="{{ __('addresses.label') }}"
                            autocomplete="off"
                            wire:model.live.debounce="label"
                        />
                        <label for="label">{{ __('addresses.label') }}</label>
                    </div>
                </div>
                <div class="col-md-1 align-self-center">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="primary"
                            wire:model="primary"
                        />
                        <label class="form-check-label" for="primary">
                            {{ __('addresses.primary') }}
                        </label>
                    </div>
                </div>
                @endif
                <div class="@if($singleAddressable)col-md-9 @else col-md-5 @endif align-self-center text-end">
                    @if($adding)
                    <button
                        type="button"
                        class="btn btn-primary btn-lg me-2"
                        wire:click="addAddress()"
                    >{{ __('common.add') }}</button>
                    @else
                        <button
                            type="button"
                            class="btn btn-primary btn-lg me-2"
                            wire:click="updateAddress()"
                        >{{ __('common.update') }}</button>
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
        <div class="border rounded p-2 text-bg-light">
            <div class="row">
                <h4 class="col-12">
                    <strong>{{ __('common.linking') }}</strong> {!! $linking->prettyAddress !!}
                </h4>
                @if(!$singleAddressable)
                    <div class="col-md-4 align-self-center">
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="primary"
                                wire:model="primary"
                            />
                            <label class="form-check-label" for="primary">
                                {{ __('addresses.primary_address') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-0">
                            <input
                                type="text"
                                class="form-control"
                                id="label"
                                placeholder="{{ __('addresses.label') }}"
                                autocomplete="off"
                                wire:model.live.debounce="label"
                            />
                            <label for="label">{{ __('addresses.label') }}</label>
                        </div>
                    </div>
                @endif
                <div class="@if($singleAddressable) col-12 @else col-md-4 @endif align-self-center text-end">
                    <button
                        type="button"
                        class="btn btn-primary btn-lg me-2"
                        wire:click="linkAddress()"
                    >{{ __('addresses.link') }}</button>
                    <button
                        type="button"
                        class="btn btn-secondary btn-lg"
                        wire:click="clearForm()"
                    >{{ __('common.cancel') }}</button>
                </div>
            </div>
        </div>
    @else
        @if($primaryAddress)
            <ul class="list-group mb-1 shadow">
                <li class="list-group-item text-bg-secondary border-2 border-primary-subtle" wire:key="{{ $primaryAddress->id }}">
                    <div class="border-bottom mb-2 d-flex justify-content-between align-items-center">
                        <div>
                            {{ __('addresses.primary') }}
                            @if(!$singleAddressable)
                            {{ $primaryAddress->personal->label }}
                            @endif
                            {{ __('addresses.address') }}
                        </div>
                        <div>
                            <button
                                type="button"
                                class="btn btn-sm btn-primary p-1"
                                wire:click="editAddress({{ $primaryAddress->id }})"
                            ><i class="fa fa-edit"></i></button>
                            <button
                                type="button"
                                class="btn btn-sm btn-danger p-1"
                                wire:confirm="Are you sure you wish to unlink this address?"
                                wire:click="removeAddress({{ $primaryAddress->id }})"
                            ><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <p>{!! nl2br($primaryAddress->prettyAddress) !!}</p>
                </li>
            </ul>
        @endif
        @if(!$singleAddressable)
            <ul class="list-group" wire:sortable="updateAddressOrder">
                @foreach($addresses as $address)
                    @if($primaryAddress && $address->id == $primaryAddress->id)
                        @continue
                    @endif
                    <li class="list-group-item text-bg-secondary" wire:key="{{ $address->id }}" wire:sortable.item="{{ $address->id }}">
                        <div class="row">
                            <div wire:sortable.handle class="show-as-action col-1 align-self-center h-100">
                                <i class="fa-solid fa-grip-lines-vertical"></i>
                            </div>
                            <div class="col-11">
                                <div class="border-bottom mb-2 d-flex justify-content-between align-items-center">
                                    <div>
                                        @if($address->personal->primary)
                                            {{ __('addresses.primary') }}
                                        @endif
                                        {{ $address->personal->label }}
                                        {{ __('addresses.address') }}
                                    </div>
                                    <div>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-primary p-1"
                                            wire:click="editAddress({{ $address->id }})"
                                        ><i class="fa fa-edit"></i></button>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger p-1"
                                            wire:confirm="Are you sure you wish to unlink this address?"
                                            wire:click="removeAddress({{ $address->id }})"
                                        ><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                                <p>{!! nl2br($address->prettyAddress) !!}</p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="d-flex justify-content-center">
                <button
                    class="btn btn-success mx-auto"
                    wire:click="set('adding', true)"
                ><i class="fa fa-plus border-end pe-1 me-1"></i>{{ __('addresses.add_address') }}</button>
            </div>
        @endif
    @endif

</div>
