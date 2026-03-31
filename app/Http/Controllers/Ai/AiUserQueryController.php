<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;

class AiUserQueryController extends Controller
{
    public static function middleware()
	{
		return
			[
				'auth',

			];
	}
}
