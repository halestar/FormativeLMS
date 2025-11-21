<?php

namespace App\Http\Controllers;

use App\Classes\SessionSettings;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class HomeController extends Controller implements HasMiddleware
{
	
	public static function middleware()
	{
		return ['auth'];
	}
	
	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Contracts\Support\Renderable
	 */
	public function index()
	{
		$person = auth()->user();
		return view('home', compact('person'));
	}
}
