<?php

namespace App\Http\Controllers\Locations;

use App\Http\Controllers\Controller;
use App\Models\Locations\Building;
use App\Models\Locations\Room;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;

class RoomController extends Controller implements HasMiddleware
{
	public static function middleware()
	{
		return ['auth'];
	}
	
	public function create(Building $building = null)
	{
		Gate::authorize('has-permission', 'locations.rooms');
		$breadcrumb = [
			__('system.menu.rooms') => route('locations.buildings.index'),
			__('locations.rooms.new') => '#',
		];
		return view('locations.rooms.create', compact('breadcrumb', 'building'));
	}
	
	public function store(Request $request)
	{
		Gate::authorize('has-permission', 'locations.rooms');
		$data = $request->validate([
			'name' => 'required|max:255',
			'capacity' => 'required|min:1|max:10000',
			'area_id' => 'nullable|exists:buildings_areas,id',
		], static::errors());
		$room = new Room();
		$room->fill($data);
		$room->save();
		return redirect(route('locations.rooms.edit', $room))
			->with('success-status', __('locations.rooms.created'));
	}
	
	private static function errors(): array
	{
		return [
			'name' => __('errors.rooms.name'),
			'capacity' => __('errors.rooms.capacity'),
			'campuses' => __('errors.rooms.campuses'),
			'area_id' => __('errors.rooms.area'),
		];
	}
	
	public function show(Room $room)
	{
		$breadcrumb =
			[
				__('system.menu.rooms') => route('locations.buildings.index'),
				$room->name => '#',
			];
		return view('locations.rooms.show', compact('room', 'breadcrumb'));
	}
	
	public function edit(Room $room)
	{
		Gate::authorize('has-permission', 'locations.rooms');
		$breadcrumb =
			[
				__('system.menu.rooms') => route('locations.buildings.index'),
				$room->name => route('locations.rooms.show', $room),
				__('locations.rooms.edit') => '#',
			];
		return view('locations.rooms.edit', compact('room', 'breadcrumb'));
	}
	
	public function updateBasicInfo(Request $request, Room $room)
	{
		Gate::authorize('has-permission', 'locations.rooms');
		$data = $request->validate([
			'name' => 'required|max:255',
			'capacity' => 'required|max:10',
		], static::errors());
		$room->fill($data);
		$room->save();
		return redirect(route('locations.rooms.edit', $room))
			->with('success-status', __('locations.rooms.updated'));
	}
	
	public function updateCampuses(Request $request, Room $room)
	{
		Gate::authorize('has-permission', 'locations.rooms');
		$data = $request->validate([
			'campuses' => 'required|array|min:1',
		], static::errors());
		// we will need to create the sync array
		$sync = [];
		foreach($data['campuses'] as $campus_id)
		{
			$sync[$campus_id] =
				[
					'label' => $request->input('label_' . $campus_id, null),
					'classroom' => $request->input('classroom_' . $campus_id, false),
				];
		}
		$room->campuses()
		     ->sync($sync);
		return redirect(route('locations.rooms.edit', $room))
			->with('success-status', __('locations.rooms.updated'));
	}
}
