<p style="margin:0 0 12px 0;color:#6c757d;font-size:13px;">
    Automated message from
    <a href="{{ config('app.url') }}" style="color:#0d6efd;text-decoration:none;">{{ config('app.name') }}</a>.
    Please do not reply to this email.
</p>

<h2 style="margin:0 0 12px 0;font-size:20px;color:#212529;">New Substitute Request Submitted</h2>

<p style="margin:0 0 16px 0;color:#212529;">
    <strong>{{ $subRequest->requester->name ?? $subRequest->requester_name }}</strong> requested substitute coverage for
    <strong>{{ $subRequest->requested_for->format('m/d/Y') }}</strong>
    from <strong>{{ $subRequest->startTime()->format('h:i A') }}</strong> to
    <strong>{{ $subRequest->endTime()->format('h:i A') }}</strong>.
</p>

<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:0 0 16px 0;">
    <tr>
        <td style="background:#f8f9fa;border:1px solid #e9ecef;padding:12px;">
            <p style="margin:0 0 8px 0;font-weight:600;color:#212529;">Substitutes Contacted</p>
            @if($subs->isNotEmpty())
                <ul style="margin:0;padding-left:18px;color:#212529;">
                    @foreach($subs as $sub)
                        <li style="margin:0 0 4px 0;">{{ $sub->name }}</li>
                    @endforeach
                </ul>
            @else
                <p style="margin:0;color:#6c757d;">No substitutes were matched for this request.</p>
            @endif
        </td>
    </tr>
</table>

<p style="margin:0 0 14px 0;">
    <a
        href="{{ route('substitutes.show', $subRequest) }}"
        style="display:inline-block;background:#0d6efd;color:#ffffff;padding:10px 14px;border-radius:6px;text-decoration:none;font-weight:600;"
    >
        View Request
    </a>
</p>
