Dear {{ $substitute->name }},

<p>
    Welcome to the New Roads School substitute pool. Thank you for completing your profile.
</p>

<p>
    Your current notification settings are:
</p>
<ul>
    <li>Email notifications: {{ $substitute->email_confirmed ? 'Enabled' : 'Not enabled' }}</li>
    <li>
        Text notifications:
        @if ($substitute->sms_confirmed && filled($substitute->phone))
            Enabled ({{ $substitute->phone }})
        @else
            Not enabled
        @endif
    </li>
    <li>
        Campuses:
        @if ($substitute->campuses->isNotEmpty())
            {{ $substitute->campuses->pluck('name')->join(', ') }}
        @else
            None assigned
        @endif
    </li>
</ul>

<p>
    You will begin receiving substitute request notifications immediately based on these settings.
</p>

<p>Thank you,</p>
<p>New Roads School</p>
