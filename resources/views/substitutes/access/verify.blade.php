@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-9 col-xl-8">
                <div class="mb-4">
                    <h1 class="h3 mb-1">{{ __('features.substitutes.verify') }}</h1>
                    <p class="text-muted mb-0">{{ __('features.substitutes.verify.description') }}</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success mb-4" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div
                        class="card-body p-4 p-md-5"
                        x-data="{
                            contactConsent: {{ old('contact_consent', $sub->email_confirmed) ? 'true' : 'false' }},
                            smsConsent: {{ old('sms_consent', $sub->sms_confirmed) ? 'true' : 'false' }}
                        }"
                    >
                        <div class="mb-4">
                            <h2 class="h5 mb-2">{{ __('features.substitutes.verify.greeting', ['name' => $sub->name]) }}</h2>
                            <p class="text-muted mb-0">{{ __('features.substitutes.verify.intro') }}</p>
                        </div>

                        <form method="POST" action="{{ route('subs.verify.update') }}" novalidate>
                            @csrf
                            <input type="hidden" name="sub-access-token" value="{{ $token }}">

                            <div class="border rounded-3 p-3 p-md-4 mb-4">
                                <h3 class="h6 text-uppercase text-muted mb-3">{{ __('features.substitutes.verify.contact.heading') }}</h3>
                                <div class="form-check mb-0">
                                    <input
                                        class="form-check-input @error('contact_consent') is-invalid @enderror"
                                        type="checkbox"
                                        value="1"
                                        id="contact-consent"
                                        name="contact_consent"
                                        @checked(old('contact_consent', $sub->email_confirmed))
                                        x-model="contactConsent"
                                        @change="if (!contactConsent) smsConsent = false"
                                        required
                                    >
                                    <label class="form-check-label" for="contact-consent">
                                        {{ __('features.substitutes.verify.contact.label') }}
                                    </label>
                                    @error('contact_consent')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">{{ __('features.substitutes.verify.contact.help') }}</div>
                                </div>
                            </div>

                            <div class="border rounded-3 p-3 p-md-4 mb-4">
                                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                                    <div>
                                        <h3 class="h6 text-uppercase text-muted mb-1">{{ __('features.substitutes.verify.sms.heading') }}</h3>
                                        <p class="text-muted small mb-0">{{ __('features.substitutes.verify.sms.description') }}</p>
                                    </div>

                                    <div class="form-check form-switch m-0">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            role="switch"
                                            id="sms-consent"
                                            name="sms_consent"
                                            value="1"
                                            x-model="smsConsent"
                                            @checked(old('sms_consent', $sub->sms_confirmed))
                                            :disabled="!contactConsent"
                                        >
                                        <label class="form-check-label" for="sms-consent">
                                            {{ __('features.substitutes.verify.sms.label') }}
                                        </label>
                                    </div>
                                </div>

                                <div x-show="smsConsent" x-cloak>
                                    <label for="phone" class="form-label">{{ __('features.substitutes.verify.phone') }}</label>
                                    <input
                                        id="phone"
                                        name="phone"
                                        type="tel"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        placeholder="{{ __('features.substitutes.verify.phone.placeholder') }}"
                                        value="{{ old('phone', $sub->phone) }}"
                                        :required="contactConsent && smsConsent"
                                        :disabled="!contactConsent || !smsConsent"
                                    >
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">{{ __('features.substitutes.verify.phone.help') }}</div>
                                </div>
                            </div>

                            <div class="alert alert-light border mt-4 mb-4">
                                <div class="small text-muted">
                                    <div>{{ __('features.substitutes.verify.notice') }}</div>
                                    <div class="mt-2">{{ __('features.substitutes.verify.notice.legal') }}</div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary" :disabled="!contactConsent">{{ __('features.substitutes.verify.submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
