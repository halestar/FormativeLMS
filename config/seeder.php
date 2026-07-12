<?php

use Database\Seeders\SimpleSeeder;

$additionalAdmins = [];
$i = 1;
while (env('ADD_ADMIN_USER_' . $i, null) !== null && env('ADMIN_AUTHADMIN_AUTH_' . $i, null) !== null)
{
	$additionalAdmins[] =
		[
			'first' => env('ADD_ADMIN_FIRST_' . $i),
			'last' => env('ADD_ADMIN_LAST_' . $i),
			'email' => env('ADD_ADMIN_USER_' . $i),
			'password' => env('ADD_ADMIN_PASS_' . $i),
			'auth' => env('ADD_ADMIN_AUTH_' . $i),
			'portrait' => env('ADD_ADMIN_PORTRAIT_' . $i),
		];
	$i++;
}

return
	[
		'seederClass' => env('SEEDER_CLASS', SimpleSeeder::class),
		'admin_first' => env('ADMIN_FIRST', 'German'),
		'admin_last' => env('ADMIN_LAST', 'Kalinec'),
		'admin_email' => env('ADMIN_EMAIL', 'fablms@kalinec.net'),
		'admin_password' => env('ADMIN_PASSWORD'),
		'admin_auth' => env('ADMIN_AUTH', 'local'),
		'admin_portrait' => env('ADMIN_PORTRAIT'),

		'other_admins' => $additionalAdmins,
	];
