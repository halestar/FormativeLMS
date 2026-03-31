<div class="card h-100 border-0 shadow-sm">
    <div class="card-header bg-white border-0 pb-0">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h3 class="card-title mb-1">{{ __('passkeys::passkeys.passkeys') }}</h3>
                <p class="text-body-secondary mb-0">{{ __('passkeys::passkeys.authenticate_using_passkey') }}</p>
            </div>
            <span class="badge rounded-pill text-bg-primary px-3 py-2">{{ $passkeys->count() }}</span>
        </div>
    </div>

    <div class="card-body">
        <div class="row g-4 align-items-start">
            <div class="col-lg-5">
                <div class="bg-light rounded-4 border h-100 p-4">
                    <form id="passkeyForm" wire:submit="validatePasskeyProperties" class="row g-3">
                        <div class="col-12">
                            <label for="name" class="form-label">{{ __('passkeys::passkeys.name') }}</label>
                            <input
                                id="name"
                                autocomplete="off"
                                type="text"
                                wire:model="name"
                                placeholder="{{ __('passkeys::passkeys.name_placeholder') }}"
                                class="form-control @error('name') is-invalid @enderror @error('name', 'passkeyForm') is-invalid @enderror"
                            >
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('name', 'passkeyForm')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-primary">
                                {{ __('passkeys::passkeys.create') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-7">
                @if($passkeys->isEmpty())
                    <div class="alert alert-light border mb-0">
                        {{ __('passkeys::passkeys.no_passkeys_registered') }}
                    </div>
                @else
                    <div class="list-group border rounded-4 overflow-hidden">
                        @foreach($passkeys as $passkey)
                            <div class="list-group-item px-4 py-3" wire:key="passkey-{{ $passkey->id }}">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                    <div class="me-md-3">
                                        <div class="fw-semibold text-dark">{{ $passkey->name }}</div>
                                        <div class="small text-body-secondary">
                                            {{ __('passkeys::passkeys.last_used') }}:
                                            {{ $passkey->last_used_at?->diffForHumans() ?? __('passkeys::passkeys.not_used_yet') }}
                                        </div>
                                    </div>

                                    <div class="d-grid d-md-block">
                                        <button wire:click="deletePasskey({{ $passkey->id }})" class="btn btn-outline-danger btn-sm">
                                            {{ __('passkeys::passkeys.delete') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@include('passkeys::livewire.partials.createScript')
