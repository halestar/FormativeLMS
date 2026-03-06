@extends('layouts.subs')

@section('content')
    <div class="py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="text-center mb-4">
                    <h1 class="h3 mb-1">Substitute Verification</h1>
                    <p class="text-muted mb-0">New Roads School Substitute Request System</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
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
                        <h2 class="h5 mb-3">Hello {{ $sub->name }},</h2>
                        <p class="text-muted">
                            Please confirm your communication preferences for substitute job notifications. This information is used
                            only for substitute-request communications and related scheduling coordination.
                        </p>

                        <form method="POST" action="{{ route('subs.verify.update') }}" novalidate>
                            @csrf
                            <input type="hidden" name="sub-access-token" value="{{ $token }}">

                            <div class="form-check mb-3">
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
                                    I agree to receive substitute-request communications from New Roads School by email and/or phone for scheduling purposes.
                                </label>
                                @error('contact_consent')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    You can ask to stop future messages by contacting the substitute administrator.
                                </div>
                            </div>

                            <div class="form-check form-switch mb-3">
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
                                    I consent to receive text messages about substitute requests and schedule coordination.
                                </label>
                            </div>

                            <div class="mb-3" x-show="smsConsent" x-cloak>
                                <label for="phone" class="form-label">Mobile Phone Number</label>
                                <input
                                    id="phone"
                                    name="phone"
                                    type="tel"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    placeholder="(555) 123-4567"
                                    value="{{ old('phone', $sub->phone) }}"
                                    :required="contactConsent && smsConsent"
                                    :disabled="!contactConsent || !smsConsent"
                                >
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Message and data rates may apply based on your carrier plan. Message frequency varies by request activity.
                                </div>
                            </div>

                            <div class="alert alert-light border mt-4 mb-4">
                                <div class="small text-muted">
                                    By submitting this form, you confirm that the contact details provided are yours and that you are authorized to receive messages at this address/number.
                                    This language is a best-effort draft and may be updated after legal review.
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary" :disabled="!contactConsent">Save Preferences</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
