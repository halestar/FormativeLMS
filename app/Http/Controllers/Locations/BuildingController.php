<?php

namespace App\Http\Controllers\Locations;

use App\Http\Controllers\Controller;
use App\Models\Locations\Building;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;

class BuildingController extends Controller implements HasMiddleware
{
    private static function errors(): array
    {
        return [
            'name' => __('errors.buildings.name'),
            'areas' => __('errors.buildings.areas'),
        ];
    }

    public function index()
    {
        Gate::authorize('has-permission', 'locations.buildings');
        $breadcrumb = [ __('system.menu.rooms') => "#" ];
        return view('locations.buildings.index', compact('breadcrumb'));
    }

    public function store(Request $request)
    {
        Gate::authorize('has-permission', 'locations.buildings');
        $data = $request->validate([
            'name' => 'required|max:255',
        ], static::errors());
        $building = new Building();
        $building->fill($data);
        $building->save();
        return redirect(route('locations.buildings.show', $building))
            ->with('success-status', __('locations.buildings.created'));

    }

    public function show(Building $building)
    {
        Gate::authorize('has-permission', 'locations.buildings');
        $breadcrumb =
            [
                __('system.menu.rooms') => route('locations.buildings.index'),
                $building->name => "#",
            ];
        return view('locations.buildings.show', compact('breadcrumb', 'building'));
    }

    public function edit(Building $building)
    {
        Gate::authorize('has-permission', 'locations.buildings');
        $breadcrumb =
            [
                __('system.menu.rooms') => route('locations.buildings.index'),
                $building->name => route('locations.buildings.show', $building),
                __('locations.buildings.edit') => "#",
            ];
        return view('locations.buildings.edit', compact('breadcrumb', 'building'));
    }

    public function updateBasicInfo(Request $request, Building $building)
    {
        Gate::authorize('has-permission', 'locations.buildings');
        $data = $request->validate([
            'name' => 'required|max:255',
        ], static::errors());
        $building->fill($data);
        $building->save();
        return redirect()->back()
            ->with('success-status', __('locations.buildings.updated'));
    }
    public function updateImg(Request $request, Building $building)
    {
        Gate::authorize('has-permission', 'locations.buildings');
        $data = $request->validate([
            'img' => 'nullable|url',
        ], static::errors());
        $building->fill($data);
        $building->save();
        return redirect()->back()
            ->with('success-status', __('locations.buildings.updated'));
    }

    public function updateAreas(Request $request, Building $building)
    {
        Gate::authorize('has-permission', 'locations.buildings');
        $data = $request->validate([
            'areas' => 'required|array|min:1',
        ], static::errors());
        foreach($building->buildingAreas as $buildingArea)
        {
            if(!in_array($buildingArea->area_id, $data['areas']) && !$building->canRemoveArea($buildingArea->schoolArea))
                $data['areas'][] = $buildingArea->area_id;
        }
        $building->schoolAreas()->sync($data['areas']);
        return redirect()->back()
            ->with('success-status', __('locations.buildings.updated'));
    }

    public function destroy(Building $building)
    {
        Gate::authorize('has-permission', 'locations.buildings');
        if($building->canDelete())
        {
            $building->delete();
            return redirect(route('locations.buildings.index'))
                ->with('success-status', __('locations.buildings.deleted'));
        }
        return redirect(route('locations.buildings.index'));
    }

	public static function middleware()
	{
		return ['auth'];
	}
}
