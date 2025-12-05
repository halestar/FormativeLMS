<div class="{{ $classes }}" style="{{ $style }}">
    <div class="d-flex justify-content-between align-items-center border-bottom">
        <h3>{{ __('subjects.school.widgets.class-announcements') }}</h3>
        @if($canManage)
        <div class="text-end">
            <input
                type="checkbox"
                class="btn-check"
                id="view-all"
                wire:model.live="viewingAll"
            />
            <label
                class="btn btn-success btn-sm"
                for="view-all"
            >
                @if($viewingAll)
                    <i class="fa-solid fa-eye"></i>
                @else
                    <i class="fa-solid fa-eye-slash"></i>
                @endif
            </label>
            <button class="btn btn-primary btn-sm" wire:click="set('adding', true)"><i class="fa-solid fa-plus"></i></button>
        </div>
        @endif
    </div>
    @if($canManage && ($adding || $editing))
        <div class="border rounded-bottom bg-light p-3 mt-0 shadow-sm-inset ">
            <div class="mb-3">
                <label for="announcement_title"
                       class="form-label">{{ __('subjects.school.widgets.class-announcements.title') }}</label>
                <input type="text" id="announcement_title"
                       class="form-control @error('announcementTitle') is-invalid @enderror"
                       wire:model="announcementTitle"/>
                <x-utilities.error-display key="announcementTitle">{{ $errors->first('announcementTitle') }}</x-utilities.error-display>
            </div>

            <label for="announcement"
                   class="form-label">{{ __('subjects.school.widgets.class-announcements.announcement') }}</label>
            <div class="row mb-3">
                <div class="col-md-8">
                    <livewire:utilities.text-editor instance-id="announcement" wire:model="announcement"
                                                    :fileable="$adding? $filer: $editObj"
                                                    height="300px"
                                                    toolbar_mode="scrolling"
                                                    :key="$refreshKey"
                    />
                    <x-utilities.error-display
                            key="announcement">{{ $errors->first('announcement') }}</x-utilities.error-display>
                </div>
                <div class="col-md-4">
                    <livewire:storage.work-storage-browser
                            :fileable="$adding? $filer: $editObj"
                            :title="__('subjects.school.widgets.class-announcements.files')"
                            height="300px"
                    />
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <label for="post_from"
                               class="input-group-text">{{ __('subjects.school.widgets.class-announcements.post.from') }}</label>
                        <input type="date" class="form-control @error('postFrom') is-invalid @enderror"
                               wire:model="postFrom"/>
                        <x-utilities.error-display key="announcement">{{ $errors->first('announcement') }}</x-utilities.error-display>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <label for="post_to"
                               class="input-group-text">{{ __('subjects.school.widgets.class-announcements.post.to') }}</label>
                        <input type="date" class="form-control @error('postTo') is-invalid @enderror"
                               wire:model="postTo"/>
                        <x-utilities.error-display key="postTo">{{ $errors->first('postTo') }}</x-utilities.error-display>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <label for="post_from"
                               class="input-group-text">{{ __('subjects.school.widgets.class-announcements.color') }}</label>
                        <input type="color"
                               class="form-control form-control-color @error('announcementColor') is-invalid @enderror"
                               wire:model="announcementColor"/>
                        <x-utilities.error-display
                                key="announcementColor">{{ $errors->first('announcementColor') }}</x-utilities.error-display>
                    </div>
                </div>
                @if($adding)
                    <button type="button" class="btn btn-primary col-md-3 mx-2"
                            wire:click="addAnnouncement()">{{ __('common.add') }}</button>
                @else
                    <button type="button" class="btn btn-primary col-md-3 mx-2"
                            wire:click="updateAnnouncement()">{{ __('common.update') }}</button>
                @endif
                <button type="button" class="btn btn-secondary col-md-2 mx-2"
                        wire:click="clearAnnouncementForm()">{{ __('common.cancel') }}</button>
            </div>
            @if($adding && $classSession->viewingAs(\App\Enums\ClassViewer::FACULTY))
                <div class="add-container-footer">
                    <h4>{{ __('subjects.school.widgets.class-announcements.post.also') }}</h4>
                    <div class="row row-cols-3">
                        @foreach($self->currentClassSessions as $session)
                            <div class="form-check col">
                                <input class="form-check-input" type="checkbox" id="ow-{{ $session->id }}"
                                       value="{{ $session->id }}" wire:model="alsoPost"/>
                                <label class="form-check-label" for="ow-{{ $session->id }}">
                                    {{ $session->name_with_schedule }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
    @foreach($announcements as $announcement)
        <div
                class="announcement-container"
                style="background-color: {{ $announcement['color'] }}; color: {{ getTextHex($announcement['color']) }} !important;"
                wire:key="{{ $announcement->id }}"
        >
            <h3 class="announcement-title d-flex justify-content-between align-items-center">
                {{ $announcement->title }}
                @if($canManage)
                    <div class="announcement-control">
                        <button type="button" class="btn btn-primary btn-sm"
                                wire:click="setEdit('{{ $announcement->id }}')"><i class="fa fa-edit"></i></button>
                        <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                wire:confirm="{{ __('subjects.school.widgets.class-announcements.delete.prompt') }}"
                                wire:click="deleteAnnouncement('{{ $announcement->id }}')"
                        ><i class="fa fa-times"></i></button>
                    </div>
                @endif
            </h3>
            {!! $announcement->announcement !!}
            @if($announcement->hasFiles())
                <div class="d-flex flex-wrap justify-content-start align-items-center">
                    <span class="fw-bold me-2">
                        {{ __('subjects.school.widgets.class-announcements.files') }}:
                    </span>
                    @foreach($announcement->workFiles as $file)
                        <a href="{{ $file->url }}" class="me-2 badge text-bg-primary link-underline link-underline-opacity-0">
                            {!! $file->icon !!}
                            <span class="ms-2">{{ $file->name }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
            <div class="announcement-footer">
                {{ __('subjects.school.widgets.class-announcements.post.until', ['from' => $announcement->post_from->format('m/d'), 'until' => $announcement->post_to->format('m/d')]) }}
            </div>
        </div>
    @endforeach
</div>
