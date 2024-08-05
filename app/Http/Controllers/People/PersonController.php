<?php

namespace App\Http\Controllers\People;

use App\Http\Controllers\Controller;
use App\Models\People\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PersonController extends Controller
{
    private function errors(): array
    {
        return
        [
            'last' => __('people.profile.fields.last.error'),
        ];
    }
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function show(Person $person)
    {
        Gate::authorize('view', $person);
        $breadcrumb = [ __('people.profile.view') => "#" ];
        if(Auth::user()->id == $person->id)
            $breadcrumb = [ __('people.profile.mine') => "#" ];
        return view('people.show', compact('person', 'breadcrumb'));
    }

    public function edit(Person $person)
    {
        Gate::authorize('edit', $person);
        $breadcrumb = [ __('people.profile.view') => route('people.show', ['person' => $person->id]), __('people.profile.edit') => "#" ];
        if(Auth::user()->id == $person->id)
            $breadcrumb = [ __('people.profile.mine') => route('people.show', ['person' => $person->id]), __('people.profile.edit') => "#" ];
        return view('people.edit', compact('person', 'breadcrumb'));
    }

    public function updateBasic(Request $request, Person $person)
    {
        Gate::authorize('updateBasic', $person);
        $data = $request->validate(
            [
                'first' => 'nullable|max:255',
                'middle' => 'nullable|max:255',
                'last' => 'required|max:255',
                'nick' => 'nullable|max:255',
                'email' => 'nullable|email|max:255',
                'dob' => 'nullable|date',
                'ethnicity_id' => 'nullable|exists:crud_ethnicities,id',
                'title_id' => 'nullable|exists:crud_titles,id',
                'suffix_id' => 'nullable|exists:crud_suffixes,id',
                'honors_id' => 'nullable|exists:crud_honors,id',
                'gender_id' => 'nullable|exists:crud_gender,id',
                'pronoun_id' => 'nullable|exists:crud_pronouns,id',
                'occupation' => 'nullable|max:255',
                'job_title' => 'nullable|max:255',
                'work_company' => 'nullable|max:255',
                'salutation' => 'nullable|max:255',
                'family_salutation' => 'nullable|max:255',
            ], $this->errors());
        $person->fill($data);
        $person->save();
        return redirect(route('people.show', ['person' => $person->id]));
    }
}
