<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller implements HasMiddleware
{
	public static function middleware()
	{
		return
			[
				new Middleware('guest', except: ['logout']),
				new Middleware('auth', only: ['logout']),
			];
	}

	public function showLoginForm()
	{
		return view('auth.login');
	}

	public function logout(Request $request)
	{
		$user = Auth::user();
		Auth::guard()->logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();
		return redirect('/');
	}
}
