<?php

return
	[
		'password.reset' => 'Templado de Reset de Contraseñas de Email',
		'password.reset.subject' => 'Un Pedido de Reset de su Contraseña ha sido recibido.',
		'password.reset.description' => 'Un Correo Electrónico Tipo es enviado a los usuarios cuando intentan resetear sus contraseñas.',
		'password.reset.recipient' => 'Recipiente Nombre',
		'password.reset.recipient_email' => 'Recipiente Correo Electrónico',
		'password.reset.token' => 'Icono de Autentication',
		'password.reset.body' => <<<EOE
<p>
	You are receiving this email because we received a password reset request for your account.  Please enter the
	following code in your browser to proceed with the reset:
</p>
<div>{!! \$token !!}</div>
<p>Thank you,</p>
EOE,
	];
