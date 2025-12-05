<?php

namespace App\Http\Controllers\Locations;

use App\Http\Controllers\Controller;
use App\Http\Resources\Locations\BuildingAreaResource;
use App\Models\Locations\BuildingArea;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AreaController extends Controller implements HasMiddleware
{
	public static function middleware()
	{
		return ['auth'];
	}
	
	public function show(BuildingArea $area)
	{
		Gate::authorize('has-permission', 'locations.areas');
		$breadcrumb =
			[
				__('system.menu.rooms') => route('locations.buildings.index'),
				$area->building->name => route('locations.buildings.show', $area->building),
				$area->schoolArea->name => "#",
			];
		return view('locations.areas.show', compact('breadcrumb', 'area'));
	}
	
	public function areaMap(BuildingArea $area)
	{
		if(!Gate::allows('has-permission', 'locations.areas'))
			return response()->json([], Response::HTTP_FORBIDDEN);
		return new BuildingAreaResource($area);
	}
}
