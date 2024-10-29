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
                <div class="col-md-4">
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
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="work"
                            wire:model="work"
                        />
                        <label class="form-check-label" for="work">
                            {{ __('addresses.work_address') }}
                        </label>
                    </div>
                </div>
                <div class="col-md-6 align-self-center text-end">
                    @if($adding)
                    <button
                        type="button"
                        class="btn btn-primary btn-lg me-2"
                        wire:click="addAddress()"
                    >{{ __('addresses.add_address') }}</button>
                    @else
                        <button
                            type="button"
                            class="btn btn-primary btn-lg me-2"
                            wire:click="updateAddress()"
                        >{{ __('addresses.update_address') }}</button>
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
                <h3 class="col-12">
                    <strong>{{ __('common.adding') }}</strong> {!! $linking->prettyAddress !!}
                </h3>
                <div class="col-md-4">
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
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="work"
                            wire:model="work"
                        />
                        <label class="form-check-label" for="work">
                            {{ __('addresses.work_address') }}
                        </label>
                    </div>
                </div>
                <div class="col-md-6 align-self-center text-end">
                    <button
                        type="button"
                        class="btn btn-primary btn-lg me-2"
                        wire:click="linkAddress()"
                    >{{ __('addresses.add_address') }}</button>
                    <button
                        type="button"
                        class="btn btn-secondary btn-lg"
                        wire:click="clearForm()"
                    >{{ __('common.cancel') }}</button>
                </div>
            </div>
        </div>
    @else
        @foreach($addresses as $address)
            <div class="border rounded p-2 mb-2 text-bg-secondary" wire:key="{{ $address->id }}">
                <div class="border-bottom mb-2 d-flex justify-content-between align-items-center">
                    <div>
                        @if($address->personal->primary){{ __('addresses.primary') }} @endif
                        @if($address->personal->work){{ __('addresses.work') }} @endif
                        @if($address->personal->seasonal)
                                {{ __('addresses.seasonal_address', ['season_start' => $address->personal->season_start, 'season_end' => $address->personal->season_end]) }}
                        @endif
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
        @endforeach
        <div class="d-flex justify-content-center">
            <button
                class="btn btn-success mx-auto"
                wire:click="set('adding', true)"
            ><i class="fa fa-plus border-end pe-1 me-1"></i>{{ __('addresses.add_address') }}</button>
        </div>
    @endif

</div>
