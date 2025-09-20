@extends('layouts.integrations', ['breadcrumb' => $breadcrumb, 'selectedService' => $workService])

@section('integrator-content')
    <form action="{{ route('integrators.local.work.update') }}" method="POST">
        @csrf
        @method('PATCH')
        <h4>{{ __('integrators.local.work.settings') }}</h4>
        <div class="mb-3">
            <label for="work_disk" class="form-label">{{ __('integrators.local.work.work_disk') }}</label>
            <select class="form-select" id="work_disk" name="work_disk" aria-describedby="work_disk_help" required>
                @foreach($disks as $disk => $diskInfo)
                    <option value="{{ $disk }}" @selected($workService->data->work_disk == $disk)>{{ $disk }}</option>
                @endforeach
            </select>
            <div id="work_disk_help" class="form-text">{!! __('integrators.local.work.work_disk.description') !!}</div>
        </div>
        <div class="row row-cols">
            <button type="submit" class="btn btn-primary">{{ __('integrators.service.update') }}</button>
        </div>
    </form>
@endsection