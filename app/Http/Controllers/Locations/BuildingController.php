<?php

namespace App\Http\Controllers\Locations;

use App\Classes\Settings\StorageSettings;
use App\Classes\Storage\DocumentFile;
use App\Enums\WorkStoragesInstances;
use App\Http\Controllers\Controller;
use App\Models\Locations\Building;
use App\Models\Locations\BuildingArea;
use App\Models\Utilities\WorkFile;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;

class BuildingController extends Controller implements HasMiddleware
{
	public static function middleware()
	{
		return ['auth'];
	}
	
	public function index()
	{
		$breadcrumb = [__('system.menu.rooms') => "#"];
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
		return redirect(route('locations.buildings.edit', $building))
			->with('success-status', __('locations.buildings.created'));
		
	}
	
	private static function errors(): array
	{
		return [
			'name' => __('errors.buildings.name'),
			'areas' => __('errors.buildings.areas'),
		];
	}
	
	public function show(Building $building)
	{
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
		return redirect()
			->back()
			->with('success-status', __('locations.buildings.updated'));
	}
	
	public function updateImg(Request $request, Building $building, StorageSettings $storageSettings)
	{
		Gate::authorize('has-permission', 'locations.buildings');
		$building_img = json_decode($request->input('building_img'), true);
		if(isset($building_img['school_id']))
			$doc = DocumentFile::hydrate($building_img);
		else
			$doc = DocumentFile::hydrate($building_img[0]);
		//first, we persist the file, using the Person object as the filer.
		$connection = $storageSettings->getWorkConnection(WorkStoragesInstances::ProfileWork);
		$imgFile = $connection->persistFile($building, $doc, false);
		if($imgFile)
		{
			$building->img->useWorkfile($imgFile);
			$building->save();
		}
		return redirect()
			->back()
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
			if(!in_array($buildingArea->area_id,
					$data['areas']) && !$building->canRemoveArea($buildingArea->schoolArea))
				$data['areas'][] = $buildingArea->area_id;
		}
		$building->schoolAreas()
		         ->sync($data['areas']);
		return redirect()
			->back()
			->with('success-status', __('locations.buildings.updated'));
	}

	public function updateMap(Request $request, Building $building, BuildingArea $area)
	{
		$data = $request->validate([
			'map' => 'required|exists:work_files,id',
		], static::errors());
		$map = WorkFile::find($data['map']);
		$area->blueprint_url->useWorkfile($map);
		$area->save();
		return redirect()
			->back()
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
}
