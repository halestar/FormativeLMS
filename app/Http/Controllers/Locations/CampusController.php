<?php

namespace App\Http\Controllers\Locations;

use App\Http\Controllers\Controller;
use App\Models\Locations\Campus;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\File;

class CampusController extends Controller implements HasMiddleware
{

    private static function errors(): array
    {
        return [
            'name' => __('errors.campuses.name'),
            'abbr' => __('errors.campuses.abbr'),
            'levels' => __('errors.campuses.levels'),
        ];
    }

    public function index()
    {
        Gate::authorize('has-permission', 'locations.campuses');
        $breadcrumb = [ trans_choice('locations.campus', 2) => "#" ];
        return view('locations.campuses.index', compact('breadcrumb'));
    }

    public function store(Request $request)
    {
        Gate::authorize('has-permission', 'locations.campuses');
        $data = $request->validate([
            'name' => 'required|max:255',
            'abbr' => 'required|max:10',
        ], static::errors());
        $campus = new Campus();
        $data['order'] = Campus::count();
        $campus->fill($data);
        $campus->save();
        return redirect(route('locations.campuses.edit', ['campus' => $campus->id]))
            ->with('success-status', __('locations.campus.updated'));
    }

    public function show(Campus $campus)
    {
        Gate::authorize('has-permission', 'locations.campuses');
        $breadcrumb =
            [
                trans_choice('locations.campus', 2) => route('locations.campuses.index'),
                $campus->name => "#",
            ];
        return view('locations.campuses.show', compact('campus', 'breadcrumb'));
    }

    public function edit(Campus $campus)
    {
        Gate::authorize('has-permission', 'locations.campuses');
        $breadcrumb =
            [
                trans_choice('locations.campus', 2) => route('locations.campuses.index'),
                $campus->name => route('locations.campuses.show', ['campus' => $campus->id]),
                __('locations.campus.edit') => '#',
            ];
        return view('locations.campuses.edit', compact('campus', 'breadcrumb'));
    }

    public function updateBasicInfo(Request $request, Campus $campus)
    {
        Gate::authorize('has-permission', 'locations.campuses');
        $data = $request->validate([
            'name' => 'required|max:255',
            'abbr' => 'required|max:10',
            'title' => 'nullable|max:255',
        ], static::errors());
        $campus->fill($data);
        $campus->save();
        return redirect()->back()
            ->with('success-status', __('locations.campus.updated'));
    }

    public function updateImg(Request $request, Campus $campus)
    {
        Gate::authorize('has-permission', 'locations.campuses');
        $data = $request->validate([
            'img' => 'nullable|url',
        ], static::errors());
        $campus->fill($data);
        $campus->save();
        return redirect()->back()
            ->with('success-status', __('locations.campus.updated'));

    }

    public function updateIcon(Request $request, Campus $campus)
    {
        Gate::authorize('has-permission', 'locations.campuses');
        $data = $request->validate([
            'color_pri' => 'nullable|hex_color',
            'color_sec' => 'nullable|hex_color',
            'icon' => ['nullable', File::types(['image/svg+xml']),],
        ], static::errors());
        $campus->color_pri = $data['color_pri'];
        $campus->color_sec = $data['color_sec'];
        if(isset($data['icon']))
            $campus->icon = $request->file('icon')->get();
        $campus->save();
        return redirect()->back()
            ->with('success-status', __('locations.campus.updated'));
    }

    public function updateLevels(Request $request, Campus $campus)
    {
        Gate::authorize('has-permission', 'locations.campuses');
        $data = $request->validate([
            'levels' => 'required|array|min:1',
        ], static::errors());
        // we need to make sure that we're not trying to deactivate something we shoulnd't
        foreach($campus->levels as $level)
        {
            if(!in_array($level->id, $data['levels']) && !$campus->canRemoveLevel($level))
                $data['levels'][] = $level->id;
        }
        $campus->levels()->sync($data['levels']);
        return redirect()->back()
            ->with('success-status', __('locations.campus.updated'));
    }

    public function updateOrder(Request $request, Campus $campus, int $order)
    {
        Gate::authorize('has-permission', 'locations.campuses');
        $position = 1;
        foreach(Campus::all() as $systemCampus)
        {
            if($campus->id == $systemCampus->id)
            {
                $systemCampus->order = $order;
                $systemCampus->save();
                continue;
            }
            if($order == $position)
                $position++;
            $systemCampus->order = $position;
            $systemCampus->save();
            $position++;
        }
        return redirect()->back()
            ->with('success-status', __('locations.campus.updated'));
    }

    public function destroy(Campus $campus)
    {
        Gate::authorize('has-permission', 'locations.campuses');
        if($campus->canDelete())
        {
            $campus->delete();
            return redirect(route('locations.campuses.index'))
                ->with('success-status', __('locations.campus.deleted'));
        }
        return redirect(route('locations.campuses.index'));
    }

	public static function middleware()
	{
		return ['auth'];
	}
}
