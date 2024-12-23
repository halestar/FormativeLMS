<div class="modal-dialog modal-dialog-scrollable modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <input type="text" autocomplete="off" name="search" id="search" class="form-control" placeholder="{{ __('common.search') }}" wire:model.live="searchTerm" />
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('common.close') }}"></button>
        </div>
        @if(count($results) > 0)
        <div class="modal-body">
            <div class="list-group">
                @foreach($results as $person)
                    <a class="list-group list-group-item-action text-decoration-none my-1 py-1" href="{{ route('people.show', ['person' => $person->id]) }}">
                        <div class="d-flex justify-content-start align-items-start">
                            <img
                                class="img-fluid img-thumbnail img-icon-normal"
                                src='{!! $person->thumbnail_url !!}'
                                alt="{{ __('people.profile.thumb') }}"
                            />
                            <h3 class="ms-3 p-0 align-self-center">{{ $person->name }}</h3>
                            <div class="ms-auto badge text-bg-primary align-self-center">{{ $person->roles->pluck('name')->join(',') }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
