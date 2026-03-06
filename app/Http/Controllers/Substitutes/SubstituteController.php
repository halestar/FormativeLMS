<?php

namespace App\Http\Controllers\Substitutes;

use App\Http\Controllers\Controller;
use App\Mail\NewSubstituteVerification;
use App\Models\Locations\Campus;
use App\Models\Substitutes\Substitute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SubstituteController extends Controller
{
    public static function middleware()
    {
        return
            [
                'auth',
                'can:substitutes.admin',
            ];
    }

    public function index(Request $request)
    {
        $breadcrumb =
            [
                __('features.features') => '#',
                __('features.substitutes.requests') => route('features.substitutes.index'),
                __('features.substitutes.pool') => '#',
            ];
        $search = trim((string) $request->input('search', ''));
        $showInactive = $request->boolean('show_inactive');

        $subsQuery = Substitute::query()
            ->with('campuses:id,name');

        if (! $showInactive) {
            $subsQuery->where('active', true);
        }

        if ($search !== '') {
            $subsQuery->where('name', 'like', '%'.$search.'%');
        }

        $subs = $subsQuery->get();

        return view('substitutes.pool.index', compact('subs', 'search', 'showInactive', 'breadcrumb'));
    }

    public function create()
    {
        $campuses = Campus::all();

        return view('substitutes.pool.create', compact('campuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'campuses' => ['required', 'array', 'min:1'],
                'campuses.*' => ['string', 'exists:campuses,id'],
                'portrait' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

        $substitute = Substitute::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $substitute->campuses()->sync($validated['campuses'] ?? []);
        if ($request->hasFile('portrait')) {
            $substitute->portrait = $request->file('portrait');
            $substitute->save();
        }
        Mail::to($substitute->email)->send(new NewSubstituteVerification($substitute));

        return redirect()
            ->route('substitutes.pool.show', $substitute)
            ->with('status', 'Substitute added successfully.');
    }

    public function edit(Substitute $substitute)
    {
        $campuses = Campus::query()->orderBy('name')->get(['id', 'name']);
        $substitute->load('campuses:id,name');

        return view('substitutes.pool.edit', compact('substitute', 'campuses'));
    }

    public function update(Request $request, Substitute $substitute)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:25'],
            'campuses' => ['required', 'array', 'min:1'],
            'campuses.*' => ['string', 'exists:campuses,id'],
            'portrait' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $substitute->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ? preg_replace('/[^0-9]/', '', $validated['phone']) : null,
        ]);

        if ($request->hasFile('portrait')) {
            $substitute->portrait = $request->file('portrait');
            $substitute->save();
        }

        $substitute->campuses()->sync($validated['campuses']);

        return redirect()
            ->route('substitutes.pool.show', $substitute)
            ->with('status', 'Substitute updated successfully.');
    }
}
