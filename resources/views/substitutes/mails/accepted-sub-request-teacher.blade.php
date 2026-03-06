<p style="margin:0 0 12px 0;color:#6c757d;font-size:13px;">
    Automated message from
    <a href="https://services.newroads.org" style="color:#0d6efd;text-decoration:none;">NRS Services</a>.
    Please do not reply to this email.
</p>

<h2 style="margin:0 0 12px 0;font-size:20px;color:#212529;">Substitute Coverage Confirmed</h2>

<p style="margin:0 0 16px 0;color:#212529;">
    Coverage has been found for all (or some) of your classes.
    <strong>{{ $sub->name }}</strong>
    (<a href="mailto:{{ $sub->email }}" style="color:#0d6efd;text-decoration:none;">{{ $sub->email }}</a>)
    will cover on <strong>{{ $subRequest->requested_for->format('m/d/Y') }}</strong>
    from <strong>{{ $subRequest->subStartTime($sub)->format('h:i A') }}</strong> to
    <strong>{{ $subRequest->subEndTime($sub)->format('h:i A') }}</strong>.
</p>

<p style="margin:0 0 8px 0;font-weight:600;color:#212529;">Classes Covered</p>
@if($subRequest->coveredClasses($sub)->isNotEmpty())
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:0 0 16px 0;">
        <thead>
        <tr>
            <th align="left" style="padding:8px;border-bottom:1px solid #dee2e6;color:#495057;font-size:13px;">Class</th>
            <th align="left" style="padding:8px;border-bottom:1px solid #dee2e6;color:#495057;font-size:13px;">Start</th>
            <th align="left" style="padding:8px;border-bottom:1px solid #dee2e6;color:#495057;font-size:13px;">End</th>
        </tr>
        </thead>
        <tbody>
        @foreach($subRequest->coveredClasses($sub) as $session)
            <tr>
                <td style="padding:8px;border-bottom:1px solid #f1f3f5;color:#212529;">{{ $session->session->name }}</td>
                <td style="padding:8px;border-bottom:1px solid #f1f3f5;color:#212529;">{{ $session->start_on->format('h:i A') }}</td>
                <td style="padding:8px;border-bottom:1px solid #f1f3f5;color:#212529;">{{ $session->end_on->format('h:i A') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <p style="margin:0 0 16px 0;color:#6c757d;">No class details were found for this assignment.</p>
@endif

<p style="margin:0;color:#495057;">
    Thank you.
</p>
