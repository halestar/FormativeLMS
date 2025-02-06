<div>
    <div class="top-announcement-edit @if(!$editing) d-none @endif">
        <div class="row mb-3">
            <div class="col-11">
                <div wire:ignore>
                    <textarea
                        id="announcementText"
                        class="form-control"
                        wire:model="announcementText"></textarea>
                </div>
            </div>
            <div class="col-1">
                <button type="button" class="btn btn-success btn-sm" wire:click="saveAnnouncement()"><i class="fa fa-check"></i></button>
                <br/>
                <button type="button" class="btn btn-secondary btn-sm" wire:click="cancel()"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="row">
            <div class="col-5">
                <div class="input-group">
                    <label for="announcementColor" class="input-group-text">{{ __('subjects.school.top-announcement.color') }}</label>
                    <input type="color" id="announcementColor" wire:model="announcementColor" class="form-control form-control-color @error('announcement_color') is-invalid @endif" />
                    <x-error-display key="announcementColor">{{ $errors->first('announcementColor') }}</x-error-display>
                </div>
            </div>
            <div class="col-7">
                <div class="input-group">
                    <label for="announcementExpiry" class="input-group-text">{{ __('subjects.school.top-announcement.expiry') }}</label>
                    <input type="date" id="announcementExpiry" class="form-control @error('announcementExpiry') is-invalid @endif" wire:model="announcementExpiry" />
                    <x-error-display key="announcementExpiry">{{ $errors->first('announcementExpiry') }}</x-error-display>
                </div>
            </div>
        </div>
        <div class="border-top mt-3 w-100">
            <h4>Also Post To</h4>
            <div class="row row-cols-4 ms-2 g-2">
                @foreach($otherClasses as $session)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="session-{{ $session->id }}" value="{{ $session->id }}" wire:model="alsoPost" />
                        <label class="form-check-label" for="session-{{ $session->id }}">{{ $session->name }}</label>
                    </div>
                @endforeach
            </div>
            <div class="form-check mt-3 fs-3">
                <input
                    type="checkbox"
                    id="notify"
                    class="form-check-input"
                    wire:model="notify"
                />
                <label class="form-check-label mt-1" for="notify">{{ __('subjects.school.widgets.class-announcements.notify') }}</label>
            </div>
        </div>
    </div>
    @if(!$editing)
        @if($announcement->hasAnnouncement() || $announcement->canManageAnnouncement())
            <div class="top-announcement d-flex justify-content-between align-items-center" style="background-color: {{ $announcement->getAnnouncementColor() }}; color: {{ $announcement->getTextHex() }};">
                <div>
                    @if($announcement->hasAnnouncement())
                        {!! $announcement->getAnnouncement() !!}
                        @if($announcement->canManageAnnouncement())
                            <br/>
                            <small>Expires {{ \Carbon\Carbon::parse($announcement->getAnnouncementExpiry())->format('m/d') }}</small>
                        @endif
                    @else
                        {{ __('subjects.school.top-announcement.no') }}
                    @endif
                </div>
                @if($announcement->canManageAnnouncement())
                    <button type="button" class="btn btn-primary ms-auto" wire:click="edit()">{{ __('common.edit') }}</button>
                @endif
            </div>
        @endif
    @endif
</div>
@script
<script>
    $wire.on('init-editor', () =>
    {
        tinymce.init({
            selector: 'textarea#announcementText',
            license_key: "gpl",
            plugins: 'code table lists',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table',
            setup: function (editor) {
                editor.on('init change', function () {
                    editor.save();
                });
                editor.on('change', function () {
                    @this.set('announcementText', editor.getContent());
                });
            }
        });
    });
    $wire.on('clear-editor', () => {
        tinymce.get("announcementText").destroy();
    });
</script>
@endscript
