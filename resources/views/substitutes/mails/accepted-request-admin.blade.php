<p style="margin:0 0 12px 0;color:#6c757d;font-size:13px;">
    Automated message from
    <a href="{{ config('app.url') }}" style="color:#0d6efd;text-decoration:none;">{{ config('app.name') }}</a>.
    Please do not reply to this email.
</p>

<h2 style="margin:0 0 12px 0;font-size:20px;color:#212529;">Substitute Accepted Coverage</h2>

<p style="margin:0 0 16px 0;color:#212529;">
    <strong>{{ $campusRequest->substitute->name }}</strong> accepted coverage for
    <strong>{{ $campusRequest->subRequest->requester_name }}</strong> on
    <strong>{{ $campusRequest->subRequest->requested_for->format('m/d/Y') }}</strong>
    from <strong>{{ $campusRequest->startTime()->format('h:i A') }}</strong> to
    <strong>{{ $campusRequest->endTime()->format('h:i A') }}</strong>.
</p>

<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:0 0 16px 0;">
    <tr>
        <td style="background:#f8f9fa;border:1px solid #e9ecef;padding:12px;">
            <p style="margin:0 0 8px 0;font-weight:600;color:#212529;">Substitute Contact</p>
            <p style="margin:0;color:#212529;">
                <a href="mailto:{{ $campusRequest->substitute->email }}" style="color:#0d6efd;text-decoration:none;">
                    {{ $campusRequest->substitute->email }}
                </a>
                @if(filled($campusRequest->substitute->phone))
                    , {{ $campusRequest->substitute->phone }}
                @endif
            </p>
        </td>
    </tr>
</table>

<p style="margin:0 0 8px 0;font-weight:600;color:#212529;">Classes Covered</p>
@if($campusRequest->classRequests->isNotEmpty())
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:0 0 16px 0;">
        <thead>
        <tr>
            <th align="left" style="padding:8px;border-bottom:1px solid #dee2e6;color:#495057;font-size:13px;">Class</th>
            <th align="left" style="padding:8px;border-bottom:1px solid #dee2e6;color:#495057;font-size:13px;">Start</th>
            <th align="left" style="padding:8px;border-bottom:1px solid #dee2e6;color:#495057;font-size:13px;">End</th>
        </tr>
        </thead>
        <tbody>
        @foreach($campusRequest->classRequests as $session)
            <tr>
                <td style="padding:8px;border-bottom:1px solid #f1f3f5;color:#212529;">{{ $session->session->name }}</td>
                <td style="padding:8px;border-bottom:1px solid #f1f3f5;color:#212529;">{{ $session->start_on->format('h:i A') }}</td>
                <td style="padding:8px;border-bottom:1px solid #f1f3f5;color:#212529;">{{ $session->end_on->format('h:i A') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <p style="margin:0 0 16px 0;color:#6c757d;">No class details were found for this campus request.</p>
@endif

<p style="margin:0 0 14px 0;">
    <a
        href="{{ route('substitutes.show', $campusRequest->subRequest) }}"
        style="display:inline-block;background:#0d6efd;color:#ffffff;padding:10px 14px;border-radius:6px;text-decoration:none;font-weight:600;"
    >
        View Request
    </a>
</p>
