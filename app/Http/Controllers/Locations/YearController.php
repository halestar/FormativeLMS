<?php

namespace App\Http\Controllers\Locations;

use App\Http\Controllers\Controller;
use App\Models\Locations\Term;
use App\Models\Locations\Year;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;

class YearController extends Controller implements HasMiddleware
{
    private static function errors(): array
    {
        return [
            'label' => __('errors.years.label'),
            'year_start' => __('errors.years.start'),
            'year_end' => __('errors.years.end'),
        ];
    }

    private static function termErrors(Year $year): array
    {
        return [
            'term_label' => __('errors.terms.label'),
            'campus_id' => __('errors.terms.campus_id'),
            'term_start' => __('errors.terms.start',
                [
                    'start' => $year->year_start->format(config('lms.date_format')),
                    'end' => $year->year_end->format(config('lms.date_format')),
                ]),
            'term_end' => __('errors.terms.end',
                [
                    'start' => $year->year_start->format(config('lms.date_format')),
                    'end' => $year->year_end->format(config('lms.date_format')),
                ]),
        ];
    }

    public function index()
    {
        Gate::authorize('has-permission', 'locations.years');
        $breadcrumb = [ trans_choice('locations.years', 2) => "#" ];
        $currentYear = Year::currentYear();
        return view('locations.years.index', compact('breadcrumb', 'currentYear'));
    }

    public function store(Request $request)
    {
        Gate::authorize('has-permission', 'locations.years');
        $data = $request->validate([
            'label' => 'required|max:255',
            'year_start' => 'required|date|before_or_equal:year_end',
            'year_end' => 'required|date|after_or_equal:year_start',

        ], static::errors());
        $year = new Year();
        $year->fill($data);
        $year->save();
        return redirect()->back()
            ->with('success-status', __('locations.years.created'));
    }

    public function show(Year $year)
    {
        Gate::authorize('has-permission', 'locations.years');
        $breadcrumb =
            [
                trans_choice('locations.years', 2) => route('locations.years.index'),
                $year->label => "#",
            ];
        //$campuses = $year->campuses->groupBy(fn(Campus $item, int $key) => $item->pivot->year_id );
        $campuses = $year->campuses()->groupBy('campuses.id')->get();
        return view('locations.years.show', compact('breadcrumb', 'year', 'campuses'));
    }

    public function update(Request $request, Year $year)
    {
        Gate::authorize('has-permission', 'locations.years');
        $data = $request->validate([
            'label' => 'required|max:255',
            'year_start' => 'required|date|before_or_equal:year_end',
            'year_end' => 'required|date|after_or_equal:year_start',

        ], static::errors());
        $year->fill($data);
        $year->save();
        return redirect()->back()
            ->with('success-status', __('locations.years.updated'));
    }

    public function destroy(Year $year)
    {
        Gate::authorize('has-permission', 'locations.years');
        if($year->canDelete())
        {
            $year->delete();
            return redirect()->route('locations.years.index')
                ->with('success-status', __('locations.years.deleted'));
        }
        return redirect()->route('locations.years.index');
    }

    public function storeTerm(Request $request, Year $year)
    {
        Gate::authorize('has-permission', 'locations.terms');
        $data = $request->validate([
            'term_label' => 'required|max:255',
            'campus_id' => 'required|exists:campuses,id',
            'term_start' => 'required|date|after_or_equal:' . $year->year_start . '|before_or_equal:term_end',
            'term_end' => 'required|date|after_or_equal:term_start|before_or_equal:' . $year->year_end,

        ], static::termErrors($year));
        $term = new Term();
        $data['label'] = $data['term_label'];
        unset($data['term_label']);
        $term->fill($data);
        $year->terms()->save($term);
        return redirect()->back()
            ->with('success-status', __('locations.terms.created'));
    }

	public static function middleware()
	{
		return ['auth'];
	}
}
