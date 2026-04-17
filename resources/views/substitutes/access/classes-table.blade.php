@if($subRequest->coveredClasses($sub)->isNotEmpty())
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:0 0 16px 0;">
        <thead>
        <tr>
            <th align="left" style="padding:8px;border-bottom:1px solid #dee2e6;color:#495057;font-size:13px;">{{ __('emails.class.status.tokens.class_name') }}</th>
            <th align="left" style="padding:8px;border-bottom:1px solid #dee2e6;color:#495057;font-size:13px;">{{ __('common.time.start') }}</th>
            <th align="left" style="padding:8px;border-bottom:1px solid #dee2e6;color:#495057;font-size:13px;">{{ __('common.time.end') }}</th>
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
    <p style="margin:0 0 16px 0;color:#6c757d;">{{ __('emails.substitutes.accepted.request.sub.classes_table.no') }}</p>
@endif