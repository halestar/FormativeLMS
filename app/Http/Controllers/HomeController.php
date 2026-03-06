<?php

namespace App\Http\Controllers;

class HomeController extends Controller
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
