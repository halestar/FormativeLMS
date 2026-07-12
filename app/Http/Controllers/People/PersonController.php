<?php

namespace App\Http\Controllers\People;

use App\Classes\People\GuardedPerson;
use App\Http\Controllers\Controller;
use App\Http\Requests\People\PersonBasicFieldsRequest;
use App\Models\Locations\Campus;
use App\Models\Locations\Year;
use App\Models\People\Person;
use App\Models\SystemTables\Relationship;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PersonController extends Controller
{
	public static function middleware()
	{
		return ['auth'];
	}

	public function index(Request $request)
	{
		Gate::authorize('viewAny', Person::class);
		$breadcrumb = [__('people.school.directory') => '#'];
		$self = Auth::user();
		$currentYear = Year::currentYear();
		$itemsPerPageOptions = [10, 25, 50, 100];
		$requestedItemsPerPage =
			(int)$request->integer('items_per_page', (int)$self->getPreference('items_per_page', 10));
		$itemsPerPage = in_array($requestedItemsPerPage, $itemsPerPageOptions, true)
			? $requestedItemsPerPage
			: 10;

		if ($request->has('items_per_page'))
		{
			$self->setPreference('items_per_page', $itemsPerPage);
			$self->save();
		}

		$roleOptions = [
			''                       => 'All roles',
			SchoolRoles::$STUDENT    => __('common.student'),
			SchoolRoles::$PARENT     => __('common.parent'),
			SchoolRoles::$EMPLOYEE   => trans_choice('people.employee', 1),
			SchoolRoles::$FACULTY    => SchoolRoles::$FACULTY,
			SchoolRoles::$STAFF      => SchoolRoles::$STAFF,
			SchoolRoles::$SUBSTITUTE => SchoolRoles::$SUBSTITUTE,
		];

		$selectedRole = $request->string('role')->toString();
		if (!array_key_exists($selectedRole, $roleOptions))
		{
			$selectedRole = '';
		}

		$selectedCampus = $request->filled('campus')
			? (int)$request->integer('campus')
			: null;
		$search = trim($request->string('search')->toString());

		$people = Person::query()
		                ->with([
			                'employeeCampuses',
			                'substituteProfile.campuses',
			                'studentRecords' => function ($query) use ($currentYear)
			                {
				                $query->where('year_id', $currentYear->id)
				                      ->whereNull('end_date')
				                      ->with(['campus', 'level']);
			                },
		                ])
		                ->when($search !== '', function ($query) use ($search)
		                {
			                $query->where(function ($personQuery) use ($search)
			                {
				                $personQuery->where('first', 'like', '%' . $search . '%')
				                            ->orWhere('last', 'like', '%' . $search . '%')
				                            ->orWhere('nick', 'like', '%' . $search . '%')
				                            ->orWhere('email', 'like', '%' . $search . '%')
				                            ->orWhere('school_id', 'like', '%' . $search . '%');
			                });
		                })
		                ->when($selectedRole !== '', function ($query) use ($selectedRole, $currentYear)
		                {
			                match ($selectedRole)
			                {
				                SchoolRoles::$STUDENT => $query->whereHas('studentRecords', function ($studentQuery) use
				                (
					                $currentYear
				                )
				                {
					                $studentQuery->where('year_id', $currentYear->id)
					                             ->whereNull('end_date');
				                }),
				                SchoolRoles::$PARENT => $query->whereHas('relationships', function (
					                $relationshipQuery) use ($currentYear)
				                {
					                $relationshipQuery->wherePivot('relationship_id', Relationship::CHILD)
					                                  ->whereHas('studentRecords', function ($studentQuery) use (
						                                  $currentYear
					                                  )
					                                  {
						                                  $studentQuery->where('year_id', $currentYear->id)
						                                               ->whereNull('end_date');
					                                  });
				                }),
				                SchoolRoles::$SUBSTITUTE => $query->whereHas('substituteProfile'),
				                default => $query->role($selectedRole),
			                };
		                })
		                ->when($selectedCampus, function ($query) use ($selectedCampus, $currentYear)
		                {
			                $query->where(function ($campusQuery) use ($selectedCampus, $currentYear)
			                {
				                $campusQuery->whereHas('studentRecords', function ($studentQuery) use (
					                $selectedCampus, $currentYear
				                )
				                {
					                $studentQuery->where('campus_id', $selectedCampus)
					                             ->where('year_id', $currentYear->id)
					                             ->whereNull('end_date');
				                })->orWhereHas('relationships', function ($relationshipQuery) use (
					                $selectedCampus, $currentYear
				                )
				                {
					                $relationshipQuery->wherePivot('relationship_id', Relationship::CHILD)
					                                  ->whereHas('studentRecords', function ($studentQuery) use (
						                                  $selectedCampus, $currentYear
					                                  )
					                                  {
						                                  $studentQuery->where('campus_id', $selectedCampus)
						                                               ->where('year_id', $currentYear->id)
						                                               ->whereNull('end_date');
					                                  });
				                })->orWhereHas('employeeCampuses', function ($employeeCampusQuery) use ($selectedCampus)
				                {
					                $employeeCampusQuery->where('campuses.id', $selectedCampus);
				                })->orWhereHas('substituteProfile.campuses', function ($substituteCampusQuery) use (
					                $selectedCampus
				                )
				                {
					                $substituteCampusQuery->where('campuses.id', $selectedCampus);
				                });
			                });
		                })
		                ->paginate($itemsPerPage)
		                ->withQueryString();

		$campusOptions = Campus::query()
		                       ->get(['id', 'name', 'abbr']);

		return view(
			'people.index', compact(
				'breadcrumb',
				'people',
				'self',
				'campusOptions',
				'itemsPerPage',
				'itemsPerPageOptions',
				'roleOptions',
				'search',
				'selectedCampus',
				'selectedRole'
			)
		);
	}


	public function show(Person $person)
	{
		Gate::authorize('view', $person);
		$breadcrumb =
			[
				__('people.school.directory') => route('people.index'),
				__('people.profile.view')     => '#',
			];
		if (Auth::user()->id == $person->id)
		{
			$breadcrumb = [__('people.profile.mine') => '#'];
		}
		$self = Auth::user();
		$isSelf = $self->id == $person->id;
		$person = new GuardedPerson($person, $self);

		return view('people.show', compact('person', 'breadcrumb', 'self', 'isSelf'));
	}

	public function edit(Person $person)
	{
		Gate::authorize('edit', $person);
		$self = auth()->user();
		$isSelf = $self->id == $person->id;
		if ($isSelf)
		{
			$breadcrumb = [
				__('people.profile.mine') => route('people.show', ['person' => $person->school_id]),
				__('people.profile.edit') => '#',
			];
		}
		else
		{
			$breadcrumb = [
				__('people.profile.view') => route('people.show', ['person' => $person->school_id]),
				__('people.profile.edit') => '#',
			];
		}
		$person = new GuardedPerson($person, $self);
		return view('people.edit', compact('person', 'breadcrumb', 'isSelf'));
	}

	public function update(PersonBasicFieldsRequest $request, Person $person)
	{
		Gate::authorize('edit', $person);
		$person->fill($request->validated());
		$person->save();

		return redirect(route('people.show', ['person' => $person->school_id]))
			->with('success-status', __('people.record.updated'));
	}


	public function changeSelfPassword()
	{
		Gate::authorize('changeSelfPassword', Auth::user());
		$breadcrumb =
			[
				'Change Password' => '#',
			];
		$person = Auth::user();

		return view('people.password', compact('breadcrumb', 'person'));
	}
}
