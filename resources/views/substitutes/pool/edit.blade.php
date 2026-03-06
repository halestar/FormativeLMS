@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h1 class="h3 mb-0">Edit Substitute</h1>
            <a href="{{ route('substitutes.pool.show', $substitute) }}" class="btn btn-outline-secondary">Back to Substitute</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('substitutes.pool.update', $substitute) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-12 col-lg-8">
                            <div class="mb-3">
                                <label for="substitute-name" class="form-label">Name</label>
                                <input
                                    id="substitute-name"
                                    name="name"
                                    type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $substitute->name) }}"
                                    required
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="substitute-email" class="form-label">Email</label>
                                <input
                                    id="substitute-email"
                                    name="email"
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $substitute->email) }}"
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="substitute-phone" class="form-label">Phone</label>
                                <input
                                    id="substitute-phone"
                                    name="phone"
                                    type="text"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $substitute->phone) }}"
                                >
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label mb-0">Campuses</label>
                                    <span class="text-muted small">Select all that apply</span>
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
                                <h2 class="h6 mb-3">Portrait</h2>

                                <div class="mb-3">
                                    <p class="text-muted small mb-1">Current Image</p>
                                    <img
                                        src="{{ $substitute->portrait }}"
                                        alt="Current portrait for {{ $substitute->name }}"
                                        class="rounded border"
                                        style="max-width: 200px; height: auto;"
                                    >
                                </div>

                                <label for="substitute-portrait" class="form-label mb-1">Upload New Image</label>
                                <input
                                    id="substitute-portrait"
                                    name="portrait"
                                    type="file"
                                    accept="image/*"
                                    class="form-control @error('portrait') is-invalid @enderror"
                                >
                                <div class="form-text">Leave blank to keep the current image.</div>
                                @error('portrait')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('substitutes.pool.show', $substitute) }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
