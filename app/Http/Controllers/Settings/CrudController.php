<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;

class CrudController extends Controller implements HasMiddleware
{

    public function index()
    {
        Gate::authorize('has-permission', 'crud');
	    $breadcrumb =
		    [
			    __('system.menu.crud') => route('settings.school'),
		    ];
        return view('settings.crud.index', compact('breadcrumb'));
    }

	public static function middleware()
	{
		return ['auth'];
	}
}
