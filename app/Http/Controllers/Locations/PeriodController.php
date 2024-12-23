<?php

namespace App\Http\Controllers\Locations;

use App\Classes\Days;
use App\Http\Controllers\Controller;
use App\Models\Locations\Campus;
use App\Models\Schedules\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PeriodController extends Controller
{
    private static function errors(): array
    {
        return [
            'name' => __('errors.periods.name'),
            'abbr' => __('errors.periods.abbr'),
            'start' => __('errors.periods.abbr'),
            'end' => __('errors.periods.abbr'),
        ];
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Campus $campus)
    {
        Gate::authorize('create', Period::class, $campus);
        $breadcrumb =
            [
                trans_choice('locations.campus', 2) => route('locations.campuses.index'),
                $campus->name => route('locations.campuses.show', ['campus' => $campus->id]),
                __('locations.period.new') => '#',
            ];
        return view('locations.periods.create', compact('campus', 'breadcrumb'));
    }

    public function store(Request $request, Campus $campus)
    {
        Gate::authorize('create', Period::class, $campus);
        $data = $request->validate([
            'name' => 'required|max:255',
            'abbr' => 'required|min:1|max:10',
            'day' => ['required', 'numeric', Rule::in(Days::getWeekdays())],
            'start' => 'required|date_format:H:i',
            'end' => 'required|date_format:H:i',
        ], static::errors());
        Log::debug(print_r($request->all(), true));
        $period = new Period();
        $period->fill($data);
        $period->campus_id = $campus->id;
        $period->save();
        return redirect(route('locations.campuses.show', $campus))
            ->with('success-status', __('locations.period.created'));
    }

    public function edit(Request $request, Period $period)
    {
        Gate::authorize('edit', $period);
        $breadcrumb =
            [
                trans_choice('locations.campus', 2) => route('locations.campuses.index'),
                $period->campus->name => route('locations.campuses.show', ['campus' => $period->campus_id]),
                __('locations.period.edit') => '#',
            ];
        return view('locations.periods.edit', compact('period', 'breadcrumb'));
    }

    public function update(Request $request, Period $period)
    {
        Gate::authorize('edit', $period);
        $data = $request->validate([
            'name' => 'required|max:255',
            'abbr' => 'required|min:1|max:10',
            'day' => ['required', 'numeric', Rule::in(Days::getWeekdays())],
            'start' => 'required|date_format:H:i',
            'end' => 'required|date_format:H:i',
        ], static::errors());
        $data['active'] = $request->input('active', false);
        $period->fill($data);
        $period->save();
        return redirect(route('locations.campuses.show', ['campus' => $period->campus_id]))
            ->with('success-status', __('locations.period.updated'));
    }

    public function destroy(Period $period)
    {
        Gate::authorize('delete', $period);
        $campus_id = $period->campus_id;
        $period->delete();
        return redirect(route('locations.campuses.show', ['campus' => $campus_id]))
            ->with('success-status', __('locations.period.deleted'));
    }

    public function massEdit(Campus $campus)
    {
        Gate::authorize('create', Period::class, $campus);
        $breadcrumb =
            [
                trans_choice('locations.campus', 2) => route('locations.campuses.index'),
                $campus->name => route('locations.campuses.show', ['campus' => $campus->id]),
                __('locations.period.create.mass') => '#',
            ];
        return view('locations.periods.mass', compact('campus', 'breadcrumb'));

    }
}
