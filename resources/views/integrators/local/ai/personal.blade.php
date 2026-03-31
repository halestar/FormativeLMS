@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
<div class="container">
    <h4 class="mb-4">
        {{ __('integrators.local.ai.server') }}
        @if($connection)
            <span class="badge text-bg-success ms-2">{{ __('integrators.local.ai.verified') }}</span>
        @else
            <span class="badge text-bg-danger ms-2">{{ __('integrators.local.ai.verified.no') }}</span>
        @endif
    </h4>
    <form action="{{ route('integrators.local.services.ai.config.personal.update') }}" method="POST">
        @csrf
        @error('endpoint')
        <div class="alert alert-danger" role="alert">{{ $message }}</div>
        @enderror
        <div class="input-group mb-3">
            <span class="input-group-text" id="endpoint-label">{{ __('integrators.local.ai.endpoint') }}</span>
            <input
                    type="text"
                    placeholder="http://localhost:11434"
                    name="endpoint"
                    value="{{ $connection?->data->endpoint }}"
                    class="form-control"
                    id="endpoint"
                    aria-describedby="endpoint-label"
            />
            <button type="submit" class="btn btn-primary">{{ __('integrators.local.ai.connect') }}</button>
        </div>
    </form>
    @if($connection)
        <livewire:ai.llm-manager :connection_id="$connection->id" />
    @endif
</div>
@endsection