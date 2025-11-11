<div class="card">
    <h3 class="card-header">{{ __('settings.storage.mimes.system') }}</h3>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <ul class="list-group" style="max-height: 600px; overflow-y: auto;">
                    @foreach($mimeTypes as $mimeType)
                        <li
                                class="list-group-item list-group-item-action"
                                wire:key="{{ $mimeType->mime }}"
                                wire:click="setSelected('{{ $mimeType->mime }}')"
                        >
                            <div class="row">
                                <span class="text-lowercase fs-6 col-6">{{ $mimeType->mime }}</span>
                                <span class="text-primary col-2 text-center">
                                @if($mimeType->is_img)
                                        <i class="fa-solid fa-image"></i>
                                    @elseif($mimeType->is_video)
                                        <i class="fa-solid fa-file-video"></i>
                                    @elseif($mimeType->is_audio)
                                        <i class="fa-solid fa-file-audio"></i>
                                    @elseif($mimeType->is_document)
                                        <i class="fa-solid fa-file"></i>
                                    @endif
                                </span>
                                <span class="col-4 text-end">{!! $mimeType->icon !!}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="mimeType" class="form-label">{{ __('settings.storage.mimes.mime') }}</label>
                    <input
                            type="text"
                            class="form-control @error('mimeType') is-invalid @enderror"
                            id="mimeType"
                            placeholder="{{ __('settings.storage.mimes.mime') }}"
                            wire:model="mimeType"
                            aria-describedby="mimeTypeHelp"
                    />
                    <x-utilities.error-display
                            key="mimeType">{{ $errors->first('mimeType') }}</x-utilities.error-display>
                    <div id="mimeTypeHelp"
                         class="form-text">{{ __('settings.storage.mimes.mime.description') }}</div>
                </div>
                <div class="mb-3">
                    <label for="ext" class="form-label">{{ __('settings.storage.mimes.ext') }}</label>
                    <input
                            type="text"
                            class="form-control @error('ext') is-invalid @enderror"
                            id="ext"
                            placeholder="{{ __('settings.storage.mimes.ext') }}"
                            wire:model="ext"
                            aria-describedby="extHelp"
                    />
                    <x-utilities.error-display
                            key="ext">{{ $errors->first('ext') }}</x-utilities.error-display>
                    <div id="extHelp"
                         class="form-text">{{ __('settings.storage.mimes.ext.description') }}</div>
                </div>
                <div class="mb-3" x-data="{ iconResults: $wire.entangle('icon') }">
                    <div class="row">
                        <div class="col-8">
                            <label for="icon" class="form-label">{{ __('settings.storage.mimes.icon') }}</label>
                            <textarea
                                    type="text"
                                    class="form-control @error('icon') is-invalid @enderror"
                                    id="icon"
                                    placeholder="{{ __('settings.storage.mimes.icon') }}"
                                    wire:model="icon"
                                    aria-describedby="iconHelp"
                            ></textarea>
                            <x-utilities.error-display
                                    key="icon">{{ $errors->first('icon') }}</x-utilities.error-display>
                        </div>
                        <div class="col-4 d-flex justify-content-center align-items-center">
                            <span class="m-auto display-5" x-html="iconResults"></span>
                        </div>
                    </div>
                    <div id="iconHelp"
                         class="form-text">{{ __('settings.storage.mimes.icon.description') }}</div>
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input
                                class="form-check-input"
                                type="radio"
                                role="switch"
                                id="isImg"
                                wire:model="isImg"
                                switch
                                name="mime_type_switch"
                                aria-describedby="isImgHelp"
                        />
                        <label class="form-check-label" for="isImg">{{ __('settings.storage.mimes.img') }}</label>
                        <x-utilities.error-display
                                key="isImg">{{ $errors->first('isImg') }}</x-utilities.error-display>
                    </div>
                    <div id="isImgHelp"
                         class="form-text">{{ __('settings.storage.mimes.img.description') }}</div>
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input
                                class="form-check-input"
                                type="radio"
                                role="switch"
                                id="isVideo"
                                wire:model="isVideo"
                                switch
                                name="mime_type_switch"
                                aria-describedby="isVideoHelp"
                        />
                        <label class="form-check-label" for="isVideoHelp">{{ __('settings.storage.mimes.video') }}</label>
                        <x-utilities.error-display
                                key="isVideo">{{ $errors->first('isVideo') }}</x-utilities.error-display>
                    </div>
                    <div id="isVideoHelp"
                         class="form-text">{{ __('settings.storage.mimes.video.description') }}</div>
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input
                                class="form-check-input"
                                type="radio"
                                role="switch"
                                id="isAudio"
                                wire:model="isAudio"
                                switch
                                name="mime_type_switch"
                                aria-describedby="isAudioHelp"
                        />
                        <label class="form-check-label" for="isAudioHelp">{{ __('settings.storage.mimes.audio') }}</label>
                        <x-utilities.error-display
                                key="isAudio">{{ $errors->first('isAudio') }}</x-utilities.error-display>
                    </div>
                    <div id="isAudioHelp"
                         class="form-text">{{ __('settings.storage.mimes.audio.description') }}</div>
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input
                                class="form-check-input"
                                type="radio"
                                role="switch"
                                id="isDoc"
                                wire:model="isDoc"
                                switch
                                name="mime_type_switch"
                                aria-describedby="isDocHelp"
                        />
                        <label class="form-check-label" for="isDocHelp">{{ __('settings.storage.mimes.document') }}</label>
                        <x-utilities.error-display
                                key="isDoc">{{ $errors->first('isDoc') }}</x-utilities.error-display>
                    </div>
                    <div id="isDocHelp"
                         class="form-text">{{ __('settings.storage.mimes.document.description') }}</div>
                </div>
                <div class="row">
                    @if($selectedMimeType)
                        <button type="button" class="col btn btn-primary mx-2"
                                wire:click="update">{{ __('settings.storage.mimes.update') }}</button>
                        <button
                                type="button"
                                class="col btn btn-danger mx-2"
                                wire:click="delete"
                                wire:confirm="{{ __('settings.storage.mimes.remove.confirm') }}"
                        >{{ __('settings.storage.mimes.remove') }}</button>
                    @else
                        <button type="button" class="col btn btn-primary mx-2"
                                wire:click="add">{{ __('settings.storage.mimes.add') }}</button>
                    @endif
                    <button type="button" class="col btn btn-secondary mx-2"
                            wire:click="clear">{{ __('common.clear') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
