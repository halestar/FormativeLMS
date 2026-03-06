<p style="margin:0 0 12px 0;color:#6c757d;font-size:13px;">
    Automated message from
    <a href="{{ config('app.url') }}" style="color:#0d6efd;text-decoration:none;">{{ config('app.name') }}</a>.
    Please do not reply to this email.
</p>

<h2 style="margin:0 0 12px 0;font-size:20px;color:#212529;">New Substitute Opportunity</h2>

<p style="margin:0 0 16px 0;color:#212529;">
    Coverage is needed for <strong>{{ $subReq->requester_name }}</strong> on
    <strong>{{ $subReq->requested_for->format('m/d/Y') }}</strong>
    from <strong>{{ $subReq->startTime()->format('g:i A') }}</strong> to
    <strong>{{ $subReq->endTime()->format('g:i A') }}</strong>.
</p>

<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:0 0 16px 0;">
    <tr>
        <td style="background:#f8f9fa;border:1px solid #e9ecef;padding:12px;">
            <p style="margin:0 0 6px 0;font-weight:600;color:#212529;">Next Step</p>
            <p style="margin:0;color:#495057;">
                Use the button below to review the request and accept coverage.
            </p>
        </td>
    </tr>
</table>

<p style="margin:0 0 10px 0;">
    <a
        href="{{ $link }}"
        style="display:inline-block;background:#0d6efd;color:#ffffff;padding:10px 14px;border-radius:6px;text-decoration:none;font-weight:600;"
    >
        Review Opportunity
    </a>
</p>

<p style="margin:0;color:#6c757d;font-size:13px;">
    If the request has already been accepted, the link will show that it is no longer available.
</p>
