<?php

namespace App\Http\Controllers;

use App\Classes\SessionSettings;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
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

    public function getSessionSetting(Request $request)
    {
        $key = $request->input('key');
        $s = SessionSettings::instance();
        $default = $request->input('default', []);
        return response()->json($s->get($key, $default), 200);
    }

    public function setSessionSetting(Request $request)
    {
        $s = SessionSettings::instance();
        $key = $request->input('key');
        $value = $request->input('value');
        $s->set($key, $value);
        return response()->json([], 200);
    }

}
