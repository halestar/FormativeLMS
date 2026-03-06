@extends('layouts.subs')

@section('content')
    <div class="py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-9 col-xl-8">
                <div class="text-center mb-4">
                    <h1 class="h3 mb-1">Substitute Opportunity</h1>
                    <p class="text-muted mb-0">Please review this request and confirm if you can cover it.</p>
                </div>

                <form action="{{ route('subs.accept') }}" method="POST">
                    @csrf
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            <h2 class="h5 mb-3">Hello {{ $sub->name }},</h2>

                            <div class="alert alert-light border mb-4">
                                <div class="fw-semibold mb-1">
                                    {{ $subRequest->requester_name }} requested coverage on {{ $subRequest->requested_for->format('m/d/Y') }}.
                                </div>
                                <div class="small text-muted">
                                    Time window: {{ $subRequest->startTime()->format('h:i A') }} - {{ $subRequest->endTime()->format('h:i A') }}
                                </div>
                            </div>

                            <h3 class="h6 text-uppercase text-muted mb-2">Requested Classes</h3>
                            <div class="table-responsive mb-4">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Class</th>
                                        <th>Room</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($classRequests as $section)
                                        <tr>
                                            <td class="fw-semibold">{{ $section->session->name }}</td>
                                            <td>{{ $section->session->room->name }}</td>
                                            <td>{{ $section->start_on->format('h:i A') }}</td>
                                            <td>{{ $section->end_on->format('h:i A') }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <input type="hidden" name="token" value="{{ $token }}" />
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Accept Coverage</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div> 
@endsection
