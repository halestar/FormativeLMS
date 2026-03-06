<?php

namespace App\Http\Controllers\Substitutes;

use App\Http\Controllers\Controller;
use App\Models\Substitutes\SubstituteRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;

class SubstituteRequestController extends Controller
{
    public static function middleware()
    {
        return
            [
                'auth',
                new Middleware('can:substitutes.admin', except: ['create', 'store']),
                new Middleware('can:substitutes.request', only: ['create', 'store']),
            ];
    }

    public function index(Request $request)
    {
        $breadcrumb =
            [
                __('features.features') => '#',
                __('features.substitutes.requests') => '#',
            ];
        $person = auth()->user();

        $perPage = (int) ($person->getPreference('items_per_page', 25));
        if ($perPage <= 0) {
            $perPage = 25;
        }

        $requestedFor = trim((string) $request->input('requested_for', ''));
        $tab = (string) $request->input('tab', 'incomplete');
        if (! in_array($tab, ['incomplete', 'upcoming', 'past'], true)) {
            $tab = 'incomplete';
        }

        $baseQuery = SubstituteRequest::query()
            ->with([
                'requester:id,first,last,nick',
                'campusRequests.campus:id,name',
                'campusRequests.substitute:id,name',
            ])
            ->withCount('classRequests');

        if ($requestedFor !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $requestedFor)) {
            $baseQuery->whereDate('requested_for', $requestedFor);
        } else {
            $requestedFor = '';
        }

        $incompleteRequests = (clone $baseQuery)
            ->current()
            ->incomplete()
            ->orderBy('requested_for', 'desc')
            ->paginate($perPage, ['*'], 'incomplete_page')
            ->withQueryString();

        $upcomingRequests = (clone $baseQuery)
            ->current()
            ->completed()
            ->orderBy('requested_for', 'desc')
            ->paginate($perPage, ['*'], 'upcoming_page')
            ->withQueryString();

        $pastRequests = (clone $baseQuery)
            ->past()
            ->orderBy('requested_for', 'desc')
            ->paginate($perPage, ['*'], 'past_page')
            ->withQueryString();

        return view('substitutes.requests.index', compact(
            'incompleteRequests',
            'upcomingRequests',
            'pastRequests',
            'person',
            'tab',
            'requestedFor',
            'breadcrumb'
        ));
    }
}
