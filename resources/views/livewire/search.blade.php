<div class="modal-dialog modal-dialog-scrollable modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <input type="text" autocomplete="off" name="search" id="search" class="form-control" placeholder="{{ __('common.search') }}" wire:model.live="searchTerm" />
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        @if(count($results) > 0)
        <div class="modal-body">
            <div class="list-group">
                @foreach($results as $person)
                    <a class="list-group list-group-item-action text-decoration-none my-1 py-1" href="{{ route('people.show', ['person' => $person->id]) }}">
                        <div class="d-flex justify-content-start align-items-start">
                            <img
                                class="img-mini"
                                src='data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg>'
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
