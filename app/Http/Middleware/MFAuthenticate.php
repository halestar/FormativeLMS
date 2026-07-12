<?php

namespace App\Http\Middleware;

use App\Classes\Settings\AuthSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MFAuthenticate
{
	/**
	 * Handle an incoming request.
	 *
	 * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
	 */
	public function handle(Request $request, Closure $next): Response
	{
		$authSettings = app()->make(AuthSettings::class);
		$person = auth()->user();
		if ($person->mfa_enabled &&
		    (!$person->mfa_verified_at ||
		     $person->mfa_verified_at->addDays((int)$authSettings->mfa_timeout_days)->isPast()))
			return redirect()->route('mfa');
		return $next($request);
	}
}
