<?php

return
    [
        'password.reset' => 'Password Reset Email Template',
	    'password.reset.subject' => 'A request to reset your password has been received.',
	    'password.reset.description' => 'Email template sent to users when they\'re trying to reset their password.',
	    'password.reset.recipient' => 'Recipient\' Name',
	    'password.reset.recipient_email' => 'Recipient\' Email',
	    'password.reset.token' => 'Authentication Token',
	    'password.reset.body' => <<<EOE
<p>
	You are receiving this email because we received a password reset request for your account.  Please enter the
	following code in your browser to proceed with the reset:
</p>
<div>{!! \$token !!}</div>
<p>Thank you,</p>
EOE,
    ];
