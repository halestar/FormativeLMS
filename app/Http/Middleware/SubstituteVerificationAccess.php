<?php

namespace App\Http\Middleware;

use App\Models\Substitutes\SubstituteToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubstituteVerificationAccess
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
	 */
	public function handle(Request $request, Closure $next): Response
	{
		$token = $request->input('sub-access-token', null);
		if(!$token)
			abort(403, 'Unauthorized');
		//search for the token in the database
		$dbToken = SubstituteToken::byToken($token);
		if(!$dbToken)
			abort(403, 'Unauthorized');
		$request->attributes->set('substitute-id', $dbToken->substitute_id);
		return $next($request);
	}
}