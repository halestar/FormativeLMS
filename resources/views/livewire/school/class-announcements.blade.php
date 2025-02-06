<div>
    @if($canManage)
        @if(!($editing || $adding))
        <div class="border rounded bg-light p-3 mb-3">
            <div class="row">
                <div class="col-8">
                    <input
                        type="text"
                        id="class-announcements-title"
                        wire:model="classAnnouncementsTitle"
                        class="form-control"
                        placeholder="{{ __('subjects.school.widgets.class-announcements.title') }}"
                        wire:change="updateTitle()"
                    />
                </div>
                <button type="button" class="btn btn-primary col-2" wire:click="setAdd()">{{ __('common.add') }}</button>
                <button type="button" class="btn btn-info col-2" wire:click="toggleViewAll()">
                    @if($viewingAll)
                        {{ __('subjects.school.widgets.class-announcements.view') }}
                    @else
                        {{ __('subjects.school.widgets.class-announcements.view.all') }}
                    @endif
                </button>
            </div>
        </div>
        @endif
        <div class="border rounded bg-light p-3 @if(!($editing || $adding)) d-none @endif">
            <div class="mb-3">
                <label for="announcement_title" class="form-label">{{ __('subjects.school.widgets.class-announcements.title') }}</label>
                <input type="text" id="announcement_title" class="form-control @error('announcementTitle') is-invalid @enderror" wire:model="announcementTitle"/>
                <x-error-display key="announcementTitle">{{ $errors->first('announcementTitle') }}</x-error-display>
            </div>
            <div class="mb-3">
                <label for="announcement" class="form-label">{{ __('subjects.school.widgets.class-announcements.announcement') }}</label>
                <div wire:ignore>
                    <textarea id="announcement" class="form-control" wire:model="announcement"></textarea>
                </div>
                <x-error-display key="announcement">{{ $errors->first('announcement') }}</x-error-display>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <label for="post_from" class="input-group-text">{{ __('subjects.school.widgets.class-announcements.post.from') }}</label>
                        <input type="date" class="form-control @error('postFrom') is-invalid @enderror" wire:model="postFrom"/>
                        <x-error-display key="announcement">{{ $errors->first('announcement') }}</x-error-display>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <label for="post_to" class="input-group-text">{{ __('subjects.school.widgets.class-announcements.post.to') }}</label>
                        <input type="date" class="form-control @error('postTo') is-invalid @enderror" wire:model="postTo"/>
                        <x-error-display key="postTo">{{ $errors->first('postTo') }}</x-error-display>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <label for="post_from" class="input-group-text">{{ __('subjects.school.widgets.class-announcements.color') }}</label>
                        <input type="color" class="form-control form-control-color @error('announcementColor') is-invalid @enderror" wire:model="announcementColor"/>
                        <x-error-display key="announcementColor">{{ $errors->first('announcementColor') }}</x-error-display>
                    </div>
                </div>
                @if($adding)
                    <button type="button" class="btn btn-primary col-md-3 mx-2" wire:click="addAnnouncement()">{{ __('common.add') }}</button>
                @else
                    <button type="button" class="btn btn-primary col-md-3 mx-2" wire:click="updateAnnouncement()">{{ __('common.update') }}</button>
                @endif
                <button type="button" class="btn btn-secondary col-md-2 mx-2" wire:click="clearAnnouncementForm()">{{ __('common.cancel') }}</button>
            </div>
            @if($adding)
            <div class="add-container-footer">
                <h4>{{ __('subjects.school.widgets.class-announcements.post.also') }}</h4>
                <div class="row row-cols-4">
                    @foreach($sessionWidgets as $session)
                        @foreach($session['widgets'] as $w)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ow-{{ $w->getId() }}" value="{{ $w->getId() }}" wire:model="alsoPost" />
                                <label class="form-check-label" for="ow-{{ $w->getId() }}">{{ $w->getTitle() }} ({{ $session['session']->name }})</label>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
            @elseif($editing)
                <div class="add-container-footer">
                    <div class="form-check">
                        <input
                            type="checkbox"
                            id="notify"
                            wire:model="notify"
                            class="form-check-input"
                        />
                        <label for="notify" class="form-check-label">{{ __('subjects.school.widgets.class-announcements.notify') }}</label>
                    </div>
                </div>
            @endif
        </div>
    @endif
    <h3 class="class-announcements-title">{{ $widget->getTitle() }}</h3>
    @foreach($announcements as $announcement)
        <div
            class="announcement-container"
            style="background-color: {{ $announcement['color'] }}; color: {{ getTextHex($announcement['color']) }} !important;"
            wire:key="{{ $announcement['id'] }}"
        >
            <h3 class="announcement-title d-flex justify-content-between align-items-center">
                {{ $announcement['title'] }}
                @if($canManage)
                <div class="announcement-control">
                    <button type="button" class="btn btn-primary btn-sm" wire:click="setEdit('{{ $announcement['id'] }}')"><i class="fa fa-edit"></i></button>
                    <button
                        type="button"
                        class="btn btn-danger btn-sm"
                        wire:click="deleteAnnouncement('{{ $announcement['id'] }}')"
                        wire:confirm="{{ __('subjects.school.widgets.class-announcements.delete.prompt') }}"
                    ><i class="fa fa-times"></i></button>
                </div>
                @endif
            </h3>
            <p class="announcement-body">{!! $announcement['announcement'] !!}</p>
            <div class="announcement-footer">
                {{ __('subjects.school.widgets.class-announcements.post.until', ['from' => \Carbon\Carbon::parse($announcement['post_from'])->format('m/d'), 'until' => \Carbon\Carbon::parse($announcement['post_to'])->format('m/d')]) }}
            </div>
        </div>
    @endforeach
</div>

@script
<script>
    $wire.on('init-editor', () =>
    {
        tinymce.init({
            selector: 'textarea#announcement',
            license_key: "gpl",
            plugins: 'code table lists',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table',
            setup: function (editor) {
                editor.on('init change', function () {
                    editor.save();
                });
                editor.on('change', function () {
                    @this.set('announcement', editor.getContent());
                });
            }
        });
    });
    $wire.on('clear-editor', () => {
        tinymce.get("announcement").destroy();
    });
</script>
@endscript
