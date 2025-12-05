<div class="{{ $classes }}" style="{{ $style }}">
    @if($canManage)
    <div class="top-announcement-edit @if(!$editing) d-none @endif">
        <div class="row mb-3">
            <div class="col-11">
                <livewire:utilities.simple-text-editor wire:model.live="announcementText" instance="announcement-text" height="200px" />
            </div>
            <div class="col-1">
                <button type="button" class="btn btn-success btn-sm" wire:click="saveAnnouncement()"><i
                            class="fa fa-check"></i></button>
                <br/>
                <button type="button" class="btn btn-secondary btn-sm" wire:click="cancel()"><i class="fa fa-times"></i>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-5">
                <div class="input-group">
                    <label for="announcementColor"
                           class="input-group-text">{{ __('subjects.school.top-announcement.color') }}</label>
                    <input type="color" id="announcementColor" wire:model="announcementColor"
                           class="form-control form-control-color @error('announcement_color') is-invalid @enderror"/>
                    <x-utilities.error-display key="announcementColor">{{ $errors->first('announcementColor') }}</x-utilities.error-display>
                </div>
            </div>
            <div class="col-7">
                <div class="input-group">
                    <label for="announcementExpiry"
                           class="input-group-text">{{ __('subjects.school.top-announcement.expiry') }}</label>
                    <input type="date" id="announcementExpiry"
                           class="form-control @error('announcementExpiry') is-invalid @enderror"
                           wire:model="announcementExpiry"/>
                    <x-utilities.error-display
                            key="announcementExpiry">{{ $errors->first('announcementExpiry') }}</x-utilities.error-display>
                </div>
            </div>
        </div>
        <div class="border-top mt-3 w-100">
            <h4>Also Post To</h4>
            <div class="row row-cols-4 ms-2 g-2">
                @foreach($otherClasses as $session)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="session-{{ $session->id }}"
                               value="{{ $session->id }}" wire:model="alsoPost"/>
                        <label class="form-check-label" for="session-{{ $session->id }}">{{ $session->name }}</label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    @if(!$canManage || !$editing)
        @if($announcement->hasAnnouncement() || $canManage)
            <div class="top-announcement d-flex justify-content-between align-items-center"
                 style="background-color: {{ $announcement->color }}; color: {{ $announcement->color->getTextHex() }};">
                <div>
                    @if($announcement->hasAnnouncement())
                        {!! $announcement->announcement !!}
                        @if($canManage)
                            <br/>
                            <small>Expires {{ $announcement->expiry->format('m/d') }}</small>
                        @endif
                    @else
                        {{ __('subjects.school.top-announcement.no') }}
                    @endif
                </div>
                @if($canManage)
                    <button type="button" class="btn btn-primary ms-auto"
                            wire:click="edit()">{{ __('common.edit') }}</button>
                @endif
            </div>
        @endif
    @endif
</div>
