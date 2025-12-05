<div class="container">
    <form wire:submit="updateMessage()">
        <div class="border-bottom d-flex justify-content-between align-content-center mb-3">
            <h4>{{ __('school.messages.information') }}</h4>
            <div>
                @if($message->system)
                    <span class="badge text-bg-warning">{{ __('school.messages.system') }}</span>
                @endif
                @if($this->isDirty)
                    <span class="badge text-bg-danger">{{ __('common.dirty') }}</span>
                @else
                    <span class="badge text-bg-success">{{ __('common.saved') }}</span>
                @endif
                @if(!$message->subscribable)
                    <span class="badge text-bg-info">{{ __('school.messages.unblockable') }}</span>
                @endif
            </div>
            <div class="form-check form-switch">
                <input
                        type="checkbox"
                        class="form-check-input"
                        id="enabled"
                        wire:model.live="enabled"
                        switch
                />
                <label class="form-check-label" for="enabled">{{ __('common.enable') }}</label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9 p-3">
                <label for="name" class="form-label">{{ __('school.messages.name') }}</label>
                <input
                    type="text"
                    id="name"
                    class="form-control"
                    wire:model.blur="name"
                    required
                />
            </div>
            <div class="col-md-3 p-3 align-self-center">
                <div class="form-check form-switch">
                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="force_subscribe"
                        wire:model.live="forceSubscribe"
                        @disabled(!$message->subscribable)
                        switch
                    />
                    <label class="form-check-label" for="force_subscribe">{{ __('school.messages.subscribe.force') }}</label>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">{{ __('school.messages.description') }}</label>
            <textarea
                id="description"
                class="form-control"
                wire:model.blur="description"
            ></textarea>
        </div>

        <div class="border-bottom d-flex justify-content-between align-content-center my-3">
            <h4>{{ __('school.messages.email') }}</h4>
            <div class="form-check form-switch">
                <input
                        type="checkbox"
                        class="form-check-input"
                        id="send_email"
                        wire:model.live="sendEmail"
                        switch
                />
                <label class="form-check-label" for="send_email">{{ __('school.messages.email.send') }}</label>
            </div>
        </div>
        <div class="form-text">{{ __('school.messages.email.help') }}</div>

        <div class="row">
            <div class="col-md-9">
                <div class="input-group mb-3">
                    <span class="input-group-text">{{ __('emails.subject') }}</span>
                    <input
                            type="text"
                            class="form-control @error('subject') is-invalid @enderror"
                            wire:model.blur="subject"
                    />
                    <x-utilities.error-display
                            key="subject">{{ $errors->first('subject') }}</x-utilities.error-display>
                </div>
                <livewire:utilities.text-editor
                    wire:model.live="body"
                    instance-id="body"
                    :fileable="$message"
                    height="400"
                    :available-tokens="($message->notification_class)::availableTokens()"
                ></livewire:utilities.text-editor>
            </div>
            <div class="col-md-3">
                <livewire:storage.work-storage-browser
                    :fileable="$message"
                    :title="__('emails.attachments')"
                ></livewire:storage.work-storage-browser>
            </div>
        </div>

        <div class="border-bottom d-flex justify-content-between align-content-center my-3">
            <h4>{{ __('school.messages.short') }}</h4>
            <div class="d-flex gap-4">
                @if($canSms)
                <div class="form-check form-switch">
                    <input
                            type="checkbox"
                            class="form-check-input"
                            id="send_sms"
                            wire:model.live="sendSms"
                            switch
                    />
                    <label class="form-check-label" for="send_sms">{{ __('school.messages.sms.send') }}</label>
                </div>
                @endif
                <div class="form-check form-switch">
                    <input
                            type="checkbox"
                            class="form-check-input"
                            id="send_push"
                            wire:model.live="sendPush"
                            switch
                    />
                    <label class="form-check-label" for="send_push">{{ __('school.messages.push.send') }}</label>
                </div>
            </div>
        </div>
        <div class="form-text">{{ __('school.messages.short.help') }}</div>

        <div class="row">
            <div class="col-md-9 p-3">
                <div class="input-group mb-3">
                    <span class="input-group-text">{{ __('school.messages.short.subject') }}</span>
                    <input
                            type="text"
                            class="form-control @error('shortSubject') is-invalid @enderror"
                            wire:model.blur="shortSubject"
                    />
                    <x-utilities.error-display
                            key="shortSubject">{{ $errors->first('shortSubject') }}</x-utilities.error-display>
                </div>
                <livewire:utilities.text-token-editor
                        wire:model.live="shortBody"
                        instance-id="shortBody"
                        height="400"
                        :available-tokens="($message->notification_class)::availableTokens()"
                ></livewire:utilities.text-token-editor>
            </div>
            <div class="col-md-3 d-flex flex-column gap-2">
                <button
                    type="button"
                    class="btn btn-warning"
                    @disabled($this->isDirty || !$this->sendEmail)
                    wire:click="testEmail"
                    id="send-test-email-btn"
                >{{ __('emails.send.test') }}</button>
                <button
                        type="button"
                        class="btn btn-warning"
                        wire:click="testSms"
                        id="send-test-sms-btn"
                        @disabled($this->isDirty || !$this->sendSms)
                >{{ __('school.messages.sms.test') }}</button>
                <button
                        type="button"
                        class="btn btn-warning"
                        wire:click="testPush"
                        id="send-test-push-btn"
                        @disabled($this->isDirty || !$this->sendPush)
                >{{ __('school.messages.push.test') }}</button>
            </div>
        </div>

        <div class="row mt-3">
            <button class="col mx-2 btn btn-primary" type="submit">{{ __('common.update') }}</button>
            <button
                    class="col mx-2 btn btn-warning"
                    type="button"
                    wire:click="revert()"
                    wire:confirm="{{ __('emails.revert.confirm') }}"
            >{{ __('common.revert') }}</button>
        </div>
    </form>
</div>
@script
<script>
    $wire.on('school-message-editor.message-sent', (event) =>
    {
        element = $(event.el);
        let html = element.html();
        element.removeClass('btn-warning').addClass('btn-success');
        element.html("{{ __('school.messages.sent') }}");
        setTimeout(() => {
            element.html(html)
            element.removeClass('btn-success').addClass('btn-warning');
        }, 3000);
    });
</script>
@endscript
