@extends('layouts.subs')

@section('content')
    <div class="py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-7">
                <div class="text-center mb-4">
                    <h1 class="h3 mb-1">Request No Longer Available</h1>
                    <p class="text-muted mb-0">Substitute Request System</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5 text-center">
                        <div class="mb-3">
                            <i class="bi bi-x-circle-fill text-danger fs-1"></i>
                        </div>
                        <h2 class="h5 mb-2">This opportunity has already been claimed.</h2>
                        <p class="text-muted mb-0">
                            Thank you for responding. Another substitute has already accepted this request, so this link is no longer active.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
