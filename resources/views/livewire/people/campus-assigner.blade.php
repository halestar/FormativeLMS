<div class="w-100">
    @if($editing)
        <div class="card">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                <span>{{ __('locations.campus.user.assign') }}</span>
                <button
                    type="button"
                    class="btn btn-danger btn-sm rounded rounded-pill"
                    x-on:click="$wire.set('editing', false)"
                    aria-label="{{ trans('common.close') }}"
                >{{ __('locations.campus.editing') }}</button>
            </h5>
            <div class="card-body">
                @foreach(\App\Models\Locations\Campus::all() as $campus)
                    <div class="form-check form-switch ms-3 mb-2" wire:key="{{ $campus->id }}">
                        <label class="form-check-label" for="campus_{{ $campus->id }}">{{ $campus->name }}</label>
                        <input
                            class="form-check-input"
                            type="checkbox"
                            role="switch"
                            wire:click="changeCampus({{ $campus->id }}, {{ !$person->employeeCampuses()->where('campus_id', $campus->id)->exists()? "true": "false" }})"
                            id="campus_{{ $campus->id }}"
                            @if($person->employeeCampuses()->where('campus_id', $campus->id)->exists()) checked @endif
                        />
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <h6 class="d-flex justify-content-between align-items-baseline">
            <div>
                <strong class="me-2">{{ trans_choice('locations.campus',2) }}:</strong> {{ $person->employeeCampuses->pluck('name')->join(', ') }}
            </div>
            @can('people.edit')
                <button
                    type="button"
                    x-on:click="$wire.set('editing', true)"
                    class="btn btn-primary btn-sm rounded rounded-pill"
                >{{ __('locations.campus.edit') }}</button>
            @endcan
        </h6>
    @endif
</div>
