@extends('layouts.subs')

@section('content')
    <div class="py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <i class="bi bi-check-circle-fill text-success fs-3"></i>
                            <div>
                                <h1 class="h3 mb-1">Thank You, {{ $sub->name }}</h1>
                                <p class="text-muted mb-0">Your response has been received.</p>
                            </div>
                        </div>

                        <div class="alert alert-success mb-3" role="alert">
                            You have confirmed that you consent to be contacted by New Roads School regarding substitute requests and schedule coordination.
                        </div>

                        @if ($sub->sms_confirmed && filled($sub->phone))
                            <div class="alert alert-light border mb-0" role="alert">
                                You also consented to receive text messages. We will use the mobile number you provided:
                                <strong>{{ $sub->phone }}</strong>.
                            </div>
                        @else
                            <div class="alert alert-light border mb-0" role="alert">
                                You did not opt in to text messages at this time. You will still receive substitute-request communications by email.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
