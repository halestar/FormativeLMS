<div class="container">
    @if($editing)
        <form wire:submit="updateEmail()" wire:show="editing">
            <div class="alert mb-3 @if($this->isDirty) alert-warning @else alert-info @endif">
                <div class="border-bottom alert-heading d-flex justify-content-between align-items-center">
                    <h5>{{ $emailSetting->setting_name }}</h5>
                    @if($this->isDirty)
                        <span class="badge text-bg-danger">{{ __('common.dirty') }}</span>
                    @else
                        <span class="badge text-bg-success">{{ __('common.saved') }}</span>
                    @endif
                </div>
                <p>{!! $emailSetting->setting_description !!}</p>
            </div>
            @error('content')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div class="row">
                <div class="col-md-9">
                    <div class="input-group mb-3">
                        <span class="input-group-text">{{ __('emails.subject') }}</span>
                        <input
                                type="text"
                                class="form-control @error('subject') is-invalid @enderror"
                                wire:model="subject"
                        />
                        <x-utilities.error-display key="subject">{{ $errors->first('subject') }}</x-utilities.error-display>
                    </div>
                    <livewire:utilities.text-editor
                            wire:model.live.debounce="content"
                            instance-id="content"
                            :fileable="$emailSetting"
                            :availableTokens="($emailClass)::availableTokens()"
                            :key="$reloadKey"
                    ></livewire:utilities.text-editor>
                </div>
                <div class="col-md-3">
                    <livewire:storage.work-storage-browser :fileable="$emailSetting"/>
                </div>
            </div>

            <div class="row mt-3">
                <button class="col mx-2 btn btn-primary" type="submit">{{ __('common.update') }}</button>
                @if(!$this->isDirty)
                    <button
                            class="col mx-2 btn btn-info"
                            type="button"
                            wire:click="send()"
                    >{{ __('emails.send.test') }}</button>
                @else
                    <button
                            class="col mx-2 btn btn-warning"
                            type="button"
                            wire:click="revert()"
                            wire:confirm="{{ __('emails.revert.confirm') }}"
                    >{{ __('common.revert') }}</button>
                @endif
                <button class="col mx-2 btn btn-secondary" wire:click="close()"
                        type="button">{{ __('common.close') }}</button>
            </div>
        </form>
    @else
        <div class="d-flex flex-wrap">
            @foreach(\App\Classes\Settings\EmailSetting::allEmails() as $emailClass)
                <div
                        class="border border-dark rounded p-2 m-2 text-bg-light show-as-action"
                        wire:click="loadEmail('{!! str_replace("\\", "\\\\", $emailClass) !!}')"
                >
                    <h5 class="border-bottom">{{ $emailClass::getSetting()->setting_name }}</h5>
                    <p>{!! $emailClass::getSetting()->setting_description !!}</p>
                </div>
            @endforeach
        </div>
    @endif
</div>
