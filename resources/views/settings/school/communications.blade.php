@inject('commSettings','App\Classes\Settings\CommunicationSettings')
<div class="row mt-3">
    <div class="col-md-6">
        <form action="{{ route('settings.school.update.communications') }}" method="post">
            @csrf
            @method('PATCH')
            <div class="card">
                <h3 class="card-header">{{ __('settings.communications') }}</h3>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="email_connection_id">{{ __('settings.communications.email.connection') }}</label>
                        <select
                            aria-describedby="email_connection_id_help"
                            name="email_connection_id"
                            id="email_connection_id"
                            class="form-select"
                        >
                            <option value="">{{ __('settings.communications.email.connection.select') }}</option>
                            @foreach($commSettings->availableEmailConnections() as $conn)
                                <option value="{{ $conn->id }}" @selected($conn->id == $commSettings->email_connection_id)>
                                    {{ $conn->service->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-utilities.error-display
                                key="email_connection_id">{{ $errors->first('email_connection_id') }}</x-utilities.error-display>
                        <div id="email_connection_id_help" class="form-text">
                            {{ __('settings.communications.email.connection.help') }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email_from">{{ __('settings.communications.email.from') }}</label>
                        <input
                            type="text"
                            name="email_from"
                            id="email_from"
                            class="form-control"
                            value="{{ $commSettings->email_from }}"
                            aria-describedby="email_from_help"
                        />
                        <x-utilities.error-display
                                key="email_from">{{ $errors->first('email_from') }}</x-utilities.error-display>
                        <div id="email_connection_id_help" class="form-text">
                            {{ __('settings.communications.email.from.help') }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="email_from_address">{{ __('settings.communications.email.from.address') }}</label>
                        <input
                                type="text"
                                name="email_from_address"
                                id="email_from_address"
                                class="form-control"
                                value="{{ $commSettings->email_from_address }}"
                                aria-describedby="email_from_address_help"
                        />
                        <x-utilities.error-display
                                key="email_from_address">{{ $errors->first('email_from_address') }}</x-utilities.error-display>
                        <div id="email_from_address_help" class="form-text">
                            {{ __('settings.communications.email.from.address.help') }}
                        </div>
                    </div>

                    @if($commSettings->canSendSms())
                        <div class="form-check form-switch mb-3">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="1"
                                id="send_sms"
                                name="send_sms"
                                switch
                                aria-describedby="send_sms_help"
                                @checked($commSettings->send_sms)
                            />
                            <label class="form-check-label" for="send_sms">
                                {{ __('settings.communications.sms.send') }}
                            </label>
                            <x-utilities.error-display
                                    key="send_sms">{{ $errors->first('send_sms') }}</x-utilities.error-display>
                            <div id="send_sms_help" class="form-text">
                                {{ __('settings.communications.sms.send.help') }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="sms_connection_id">{{ __('settings.communications.sms.connection') }}</label>
                            <select
                                    aria-describedby="sms_connection_id_help"
                                    name="sms_connection_id"
                                    id="sms_connection_id"
                                    class="form-select"
                            >
                                <option value="">{{ __('settings.communications.sms.connection.select') }}</option>
                                @foreach($commSettings->availableSmsConnections() as $conn)
                                    <option value="{{ $conn->id }}" @selected($conn->id == $commSettings->sms_connection_id)>
                                        {{ $conn->service->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-utilities.error-display
                                    key="sms_connection_id">{{ $errors->first('sms_connection_id') }}</x-utilities.error-display>
                            <div id="sms_connection_id_help" class="form-text">
                                {{ __('settings.communications.sms.connection.help') }}
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            {{ __('settings.communications.sms.send.no') }}
                        </div>
                    @endif
                </div>
                <div class="card-footer ">
                    <div class="row justify-content-center">
                        <button type="submit"
                                class="btn btn-primary col">{{ __('settings.communications.update') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-6">
        <div class="card">
            <h3 class="card-header">{{ __('settings.communications.messages.system') }}</h3>
            <div class="list-group list-group-flush">
                @foreach($systemMessages as $message)
                    <a
                        class="list-group-item list-group-item-action"
                        href="{{ route('settings.school.messages', $message->id) }}"
                    >
                        {{ $message->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>