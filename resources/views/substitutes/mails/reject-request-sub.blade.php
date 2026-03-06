<p style="margin:0 0 12px 0;color:#6c757d;font-size:13px;">
    Automated message from
    <a href="{{ config('app.url') }}" style="color:#0d6efd;text-decoration:none;">{{ config('app.name') }}</a>
    Please do not reply to this email.
</p>

<h2 style="margin:0 0 12px 0;font-size:20px;color:#212529;">Coverage Has Been Filled</h2>

<p style="margin:0 0 16px 0;color:#212529;">
    Thank you for your quick response and continued support.
    Coverage has now been finalized for
    <strong>{{ $subReq->requester_name }}</strong> on
    <strong>{{ $subReq->requested_for->format('m/d/Y') }}</strong>
    from <strong>{{ $subReq->startTime()->format('g:i A') }}</strong> to
    <strong>{{ $subReq->endTime()->format('g:i A') }}</strong>.
</p>

<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:0 0 16px 0;">
    <tr>
        <td style="background:#f8f9fa;border:1px solid #e9ecef;padding:12px;">
            <p style="margin:0;color:#495057;">
                No action is needed on your part for this request. We appreciate your availability and will continue
                to notify you when future opportunities are open.
            </p>
        </td>
    </tr>
</table>

<p style="margin:0;color:#495057;">
    Thank you again for helping support New Roads School.
</p>
