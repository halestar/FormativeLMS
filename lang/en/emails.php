<?php

return
[
	'content' => 'Email Body',
	'password.reset' => 'Password Reset Email Template',
	'password.reset.body' => <<<EOE
<p>
You are receiving this email because we received a password reset request for your account.  Please enter the
following code in your browser to proceed with the reset:
</p>
<div>{!! \$token !!}</div>
<p>Thank you,</p>
EOE,
	'password.reset.description' => 'Email template sent to users when they\'re trying to reset their password.',
	'password.reset.recipient' => 'Recipient\'s Name',
	'password.reset.recipient_email' => 'Recipient\'s Email',
	'password.reset.subject' => 'A request to reset your password has been received.',
	'password.reset.token' => 'Authentication Token',
	'preview' => 'Preview Email',
	'revert.confirm' => 'Are you sure you wish to undo your changes?',
	'send.test' => 'Send Test Email to Myself',
	'subject' => 'Subject',
	'test.sent.message' => 'A test message with this content was sent to :email',
	'test.sent.title' => 'Test Message Sent',
];