Dear {{ $substitute->name }},
<p>
    If you're receiving this message, you have been signed up for the New Roads School substitute system.
    This system allows our teachers to request substitutes for a day they will be absent and transmit this
    request to all substitutes available and gives them a chance to accept the job.  This email is to verify
    that you have signed up for this program and to gather some communication information about yourself.
    If this was sent to you in error, please feel free to disregard this email. You can also contact
    <a href="mailto:gkalinec@newroads.org">the administrator</a> to ask to be removed.
</p>
<p>
    If you're interested in becoming a substitute for New Roads School, please complete
    <a href="{{ route('subs.verify', ['sub-access-token' => $plainTextToken]) }}">this verification form</a>.
</p>
<p>Thank you,</p>
<p>New Roads School</p>
