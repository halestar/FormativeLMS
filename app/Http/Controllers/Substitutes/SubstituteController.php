<?php

namespace App\Http\Controllers\Substitutes;

use App\Http\Controllers\Controller;
use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Http\Request;

class SubstituteController extends Controller
{
	public static function middleware()
	{
		return
			[
				'auth',
				'permission:substitute.admin',
			];
	}

	public function index(Request $request)
	{
		$breadcrumb =
			[
				__('features.features')                          => '#',
				trans_choice('features.substitutes.requests', 2) => route('features.substitutes.index'),
				__('features.substitutes.pool')                  => '#',
			];
		$search = trim((string)$request->input('search', ''));
		$showInactive = $request->boolean('show_inactive');
		$roles = [SchoolRoles::$SUBSTITUTE];
		if ($showInactive)
		{
			$roles[] = SchoolRoles::$OLD_SUBSTITUTE;
		}
		if ($search !== '')
		{
			$subsQuery = Person::search($search)->whereIn('roles', $roles);
		}
		else
		{
			$subsQuery = Person::whereHas('roles', fn ($q) => $q->whereIn('name', $roles));
		}

		$subs = $subsQuery->with('substituteProfile')->get();

		return view('substitutes.pool.index', compact('subs', 'search', 'showInactive', 'breadcrumb'));
	}
}
