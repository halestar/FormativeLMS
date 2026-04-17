<?php

namespace App\Http\Controllers\Substitutes;

use App\Http\Controllers\Controller;
use App\Models\Locations\Campus;
use App\Models\Substitutes\Substitute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SubstituteController extends Controller
{
    public static function middleware()
    {
        return
            [
                'auth',
                'can:substitute.admin',
            ];
    }

    public function index(Request $request)
    {
        $breadcrumb =
            [
                __('features.features') => '#',
                trans_choice('features.substitutes.requests', 2) => route('features.substitutes.index'),
                __('features.substitutes.pool') => '#',
            ];
        $search = trim((string) $request->input('search', ''));
        $showInactive = $request->boolean('show_inactive');

        $subsQuery = Substitute::query()
            ->with(['campuses:id,name', 'person']);

        if (! $showInactive) {
            $subsQuery->where('active', true);
        }

        if ($search !== '') {
            $terms = preg_split('/[\s,]+/', $search, -1, PREG_SPLIT_NO_EMPTY);

            $subsQuery->whereHas('person', function (Builder $query) use ($terms) {
                foreach ($terms as $term) {
                    $query->where(function (Builder $query) use ($term) {
                        $query->where('first', 'like', '%'.$term.'%')
                            ->orWhere('middle', 'like', '%'.$term.'%')
                            ->orWhere('last', 'like', '%'.$term.'%')
                            ->orWhere('nick', 'like', '%'.$term.'%')
                            ->orWhere('email', 'like', '%'.$term.'%');
                    });
                }
            });
        }

        $subs = $subsQuery->get();

        return view('substitutes.pool.index', compact('subs', 'search', 'showInactive', 'breadcrumb'));
    }

    public function edit(Substitute $substitute)
    {
        $campuses = Campus::query()->orderBy('name')->get(['id', 'name']);
        $substitute->load(['campuses:id,name', 'person.phones']);

        return view('substitutes.pool.edit', compact('substitute', 'campuses'));
    }

    public function update(Request $request, Substitute $substitute)
    {
        $validated = $request->validate([
            'phone_id' => ['nullable', 'integer', 'exists:phones,id'],
            'campuses' => ['required', 'array', 'min:1'],
            'campuses.*' => ['string', 'exists:campuses,id'],
        ]);

        $phoneId =

        $substitute->phone_id = $validated['phone_id'] ?? null;
		$substitute->save();

        $substitute->campuses()->sync($validated['campuses']);

        return redirect()
            ->route('features.substitutes.pool.show', $substitute->person->school_id)
            ->with('success-status', __('features.substitutes.pool.updated'));
    }
}
