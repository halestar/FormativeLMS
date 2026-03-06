@extends('layouts.subs')

@section('content')
    <div class="py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-7">
                <div class="text-center mb-4">
                    <h1 class="h3 mb-1">Coverage Accepted</h1>
                    <p class="text-muted mb-0">Substitute Request System</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5 text-center">
                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill text-success fs-1"></i>
                        </div>
                        <h2 class="h5 mb-2">Thank you, {{ $sub->name }}.</h2>
                        <p class="mb-3 text-muted">
                            You are now confirmed to cover
                            <strong>{{ $subRequest->requester->name ?? $subRequest->requester_name }}</strong>'s classes on
                            <strong>{{ $subRequest->requested_for->format('m/d/Y') }}</strong>.
                        </p>
                        <div class="alert alert-light border mb-0">
                            A confirmation email with your coverage details and schedule has been sent.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
