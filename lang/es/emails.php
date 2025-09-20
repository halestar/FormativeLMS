<?php

return
	[
		'content' => 'English: Email Body',
		'password.reset' => 'Templado de Reset de Contraseñas de Email',
		'password.reset.body' => <<<EOE
<p>
	You are receiving this email because we received a password reset request for your account.  Please enter the
	following code in your browser to proceed with the reset:
</p>
<div>{!! \$token !!}</div>
<p>Thank you,</p>
EOE,
		'password.reset.description' => 'Un Correo Electrónico Tipo es enviado a los usuarios cuando intentan resetear sus contraseñas.',
		'password.reset.recipient' => 'Recipiente Nombre',
		'password.reset.recipient_email' => 'Recipiente Correo Electrónico',
		'password.reset.subject' => 'Un Pedido de Reset de su Contraseña ha sido recibido.',
		'password.reset.token' => 'Icono de Autentication',
		'preview' => 'English: Preview Email',
		'revert.confirm' => 'English: Are you sure you wish to undo your changes?',
		'send.test' => 'English: Send Test Email to Myself',
		'subject' => 'English: Subject',
		'test.sent.message' => 'English: A test message with this content was sent to :email',
		'test.sent.title' => 'English: Test Message Sent',
	];