@extends('layouts.integrations', ['breadcrumb' => $breadcrumb, 'selectedService' => $documentsService])

@section('integrator-content')
    <form action="{{ route('integrators.local.documents.update') }}" method="POST">
        @csrf
        @method('PATCH')
        <h4>{{ __('integrators.local.documents.settings') }}</h4>
        <div class="mb-3">
            <label for="documents_disk" class="form-label">{{ __('integrators.local.documents.documents_disk') }}</label>
            <select class="form-select" id="documents_disk" name="documents_disk" aria-describedby="documents_disk_help" required>
                @foreach($disks as $disk => $diskInfo)
                    <option value="{{ $disk }}" @selected($documentsService->data->documents_disk == $disk)>{{ $disk }}</option>
                @endforeach
            </select>
            <div id="documents_disk_help" class="form-text">{!! __('integrators.local.documents.documents_disk.description') !!}</div>
        </div>
        <div class="row row-cols">
            <button type="submit" class="btn btn-primary">{{ __('integrators.service.update') }}</button>
        </div>
    </form>
@endsection