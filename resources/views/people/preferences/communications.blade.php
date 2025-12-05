@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <form method="POST" action="{{ route('people.preferences.communications.update.delivery', $person) }}">
                @csrf
                @method('PATCH')
                <div class="card">
                    <h4 class="card-header">{{ __('people.preferences.communications.channels') }}</h4>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="1"
                                id="send_email"
                                name="send_email"
                                aria-describedby="send_email_help"
                                @checked($person->getPreference('communications.send_email'))
                                switch
                            >
                            <label class="form-check-label" for="send_email">
                                {{ __('school.messages.email.send') }}
                            </label>
                        </div>
                        <div class="form-text mb-3" id="send_email_help">
                            {{ __('people.preferences.communications.channels.email', ['email' => $person->system_email]) }}
                        </div>
                        @if($phones->count() == 0)
                            <div class="alert alert-warning mb-3">
                                {{ __('people.preferences.communications.channels.sms.no') }}
                            </div>
                        @else
                            <div class="form-check form-switch">
                                <input
                                        class="form-check-input"
                                        type="checkbox"
                                        value="1"
                                        id="send_sms"
                                        name="send_sms"
                                        @checked($person->getPreference('communications.send_sms'))
                                        switch
                                >
                                <label class="form-check-label" for="send_sms">
                                    {{ __('school.messages.sms.send') }}
                                </label>
                            </div>
                            <div class="form-text mb-3">
                                {!! __('people.preferences.communications.channels.sms', ['tos' => $schoolSettings->terms_of_service, 'priv' => $schoolSettings->privacy_policy]) !!}
                            </div>
                            <div class="mb-3">
                                <label for="sms_phone_id" class="form-label">
                                    {{ __('people.preferences.communications.channels.sms.phone') }}
                                </label>
                                <select class="form-select" id="sms_phone_id" name="sms_phone_id">
                                    @foreach($phones as $phone)
                                        <option value="{{ $phone->id }}" @selected($person->getPreference('communications.sms_phone_id') == $phone->id)>{{ $phone->prettyPhone }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="form-check form-switch">
                            <input
                                    class="form-check-input"
                                    type="checkbox"
                                    value="1"
                                    id="send_push"
                                    name="send_push"
                                    aria-describedby="send_push_help"
                                    @checked($person->getPreference('communications.send_push'))
                                    switch
                            >
                            <label class="form-check-label" for="send_push">
                                {{ __('school.messages.push.send') }}
                            </label>
                        </div>
                        <div class="form-text mb-3" id="send_push_help">
                            {{ __('people.preferences.communications.channels.push') }}
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row"><button type="submit" class="btn btn-primary col">{{ __('common.update') }}</button></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <form method="POST" action="{{ route('people.preferences.communications.update.subscriptions', $person) }}">
                @csrf
                @method('PATCH')
                <div class="card">
                    <h4 class="card-header">{{ __('school.messages.subscriptions') }}</h4>
                    <ul class="list-group list-group-flush mt-2">
                        @foreach(\App\Models\Utilities\SchoolMessage::subscribable()->get() as $message)
                            <li class="list-group-item" wire:key="{{ $message->id }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>{{ $message->name }}</h5>
                                    <div class="form-check form-switch">
                                        <input
                                                class="form-check-input"
                                                type="checkbox"
                                                value="{{ $message->id }}"
                                                id="subscriptions_{{ $message->id }}"
                                                name="subscriptions[]"
                                                @checked($message->isSubscribed($person))
                                                switch
                                        >
                                        <label class="form-check-label" for="subscriptions_{{ $message->id }}">
                                            {{ __('school.messages.subscribe') }}
                                        </label>
                                    </div>
                                </div>
                                <p class="form-text text-muted">{!! $message->description !!}</p>
                            </li>
                        @endforeach
                    </ul>
                    <div class="card-footer">
                        <div class="row"><button type="submit" class="btn btn-primary col">{{ __('common.update') }}</button></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection