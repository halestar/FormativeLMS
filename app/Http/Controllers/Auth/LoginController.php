<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\People\Person;
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
				new Middleware('guest', except: ['logout', 'impersonate', 'unimpersonate']),
				new Middleware('auth', only: ['logout', 'impersonate', 'unimpersonate']),
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
	
	public function impersonate(Person $person)
	{
		session(['impersonating_from' => redirect()->back()->getTargetUrl()]);
		auth()->user()->impersonate($person);
		return redirect(route('home'));
	}
	
	public function unimpersonate()
	{
		$manager = app('impersonate');
		if($manager->isImpersonating())
		{
			auth()->user()->leaveImpersonation();
			$url = session()->pull('impersonating_from', route('home'));
			return redirect($url);
		}
		return redirect(route('home'));
	}
}
