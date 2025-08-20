<div class="container">
    @if($editing)
        <div class="alert alert-info mb-3">
            <h5 class="border-bottom">{{ $emailSetting->setting_name }}</h5>
            <p>{!! $emailSetting->setting_description !!}</p>
        </div>
        <div class="input-group mb-3">
            <span class="input-group-text">{{ __('emails.subject') }}</span>
            <input
                    type="text"
                    class="form-control"
                    wire:model="subject"
            />
        </div>
        <livewire:utilities.text-editor
                wire:model="content"
                :workStorage="$workStorage"
                :fileable="$emailSetting"
                :availableTokens="($emailClass)::availableTokens()"
        />
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
@push('head_scripts')
    <x-utilities.text-editor instance-name="textarea#content"/>
@endpush
