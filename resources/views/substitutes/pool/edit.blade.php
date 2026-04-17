@extends('layouts.app')

@section('content')
    <div class="container">
        @php
            $person = $substitute->person;
            $phones = $person->phones->sortBy('pivot.order')->values();
            $selectedPhoneId = old('phone_id', $substitute->phone_id);
        @endphp

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h1 class="h3 mb-1">{{ __('features.substitutes.pool.edit') }}</h1>
                <p class="text-muted mb-0">{{ __('features.substitutes.pool.edit.description') }}</p>
            </div>
            <a href="{{ route('features.substitutes.pool.show', $substitute) }}" class="btn btn-outline-secondary">{{ __('features.substitutes.pool.back') }}</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('features.substitutes.pool.update', $substitute) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-12 col-lg-8">
                            <div class="card border rounded-3 shadow-sm mb-4">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                                        <div>
                                            <h2 class="h5 mb-1">{{ __('features.substitutes.pool.edit.person') }}</h2>
                                            <p class="text-muted mb-0">{{ __('features.substitutes.pool.edit.person.description') }}</p>
                                        </div>
                                        @can('people.edit')
                                            <a href="{{ route('people.edit', ['person' => $person->school_id]) }}" class="btn btn-outline-primary">
                                                {{ __('features.substitutes.pool.edit.person.manage') }}
                                            </a>
                                        @endcan
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label text-muted small text-uppercase mb-1">{{ __('features.substitutes.pool.edit.person.name') }}</label>
                                            <div class="form-control bg-body-tertiary">{{ $person->name }}</div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label text-muted small text-uppercase mb-1">{{ __('features.substitutes.pool.edit.person.email') }}</label>
                                            <div class="form-control bg-body-tertiary">{{ $person->email ?: __('common.na') }}</div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label text-muted small text-uppercase mb-1">{{ __('features.substitutes.pool.edit.person.first') }}</label>
                                            <div class="form-control bg-body-tertiary">{{ $person->first ?: __('common.na') }}</div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label text-muted small text-uppercase mb-1">{{ __('features.substitutes.pool.edit.person.last') }}</label>
                                            <div class="form-control bg-body-tertiary">{{ $person->last ?: __('common.na') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="substitute-phone-id" class="form-label">{{ __('features.substitutes.pool.edit.primary_text_phone') }}</label>
                                <select
                                    id="substitute-phone-id"
                                    name="phone_id"
                                    class="form-select @error('phone_id') is-invalid @enderror"
                                >
                                    <option value="">{{ __('features.substitutes.pool.edit.primary_text_phone.none') }}</option>
                                    @foreach ($phones as $phone)
                                        <option value="{{ $phone->id }}" @selected((string) $selectedPhoneId === (string) $phone->id)>
                                            {{ $phone->prettyPhone }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($phones->isEmpty())
                                    <div class="form-text">{{ __('features.substitutes.pool.edit.primary_text_phone.none_available') }}</div>
                                @else
                                    <div class="form-text">{{ __('features.substitutes.pool.edit.primary_text_phone.help') }}</div>
                                @endif
                                @error('phone_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label mb-0">{{ __('system.menu.campuses') }}</label>
                                    <span class="text-muted small">{{ __('features.substitutes.pool.edit.campuses_help') }}</span>
                                </div>
                                <div class="border rounded-3 p-3">
                                    <div class="row g-3">
                                        @php
                                            $selectedCampuses = old('campuses', $substitute->campuses->pluck('id')->all());
                                        @endphp
                                        @foreach ($campuses as $campus)
                                            <div class="col-12 col-md-6">
                                                <div class="form-check form-switch m-0">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        role="switch"
                                                        name="campuses[]"
                                                        id="campus-{{ $campus->id }}"
                                                        value="{{ $campus->id }}"
                                                        @checked(in_array($campus->id, $selectedCampuses))
                                                    >
                                                    <label class="form-check-label" for="campus-{{ $campus->id }}">
                                                        {{ $campus->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @error('campuses')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                                @error('campuses.*')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="border rounded-3 p-3 h-100">
                                <h2 class="h6 mb-3">{{ __('features.substitutes.pool.edit.portrait') }}</h2>
                                <livewire:people.portrait-editor :person="$substitute->person" />
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('features.substitutes.pool.show', $substitute) }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
