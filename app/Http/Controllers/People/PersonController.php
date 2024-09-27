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
        $self = Auth::user();
        $isSelf = $self->id == $person->id;
        return view('people.show', compact('person', 'breadcrumb', 'self', 'isSelf'));
    }

    public function edit(Person $person)
    {
        Gate::authorize('edit', $person);
        $breadcrumb = [ __('people.profile.view') => route('people.show', ['person' => $person->id]), __('people.profile.edit') => "#" ];
        $self = Auth::user();
        $isSelf = $person->id == $self->id;
        if($isSelf)
            $breadcrumb = [ __('people.profile.mine') => route('people.show', ['person' => $person->id]), __('people.profile.edit') => "#" ];
        return view('people.edit', compact('person', 'breadcrumb', 'self', 'isSelf'));
    }

    public function updateBasic(Request $request, Person $person)
    {
        Gate::authorize('edit', $person);
        $person->fill($request->all());
        $person->save();
        return redirect(route('people.show', ['person' => $person->id]));
    }
}
